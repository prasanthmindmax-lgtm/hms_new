<?php
namespace App\Console\Commands;

use App\Models\TblPoEmail;
use App\Services\HmsDltPortalSmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDeadlineNotifications extends Command
{
    protected $signature   = 'notify:deadlines {--force : Clear today\'s sent records and re-send all notifications}';
    protected $description = 'Send SMS, email and web alerts for due/overdue bills, POs, quotations, and timeline-end-today (Accounts Book recipients from po_email_tbl)';

    public function handle(): int
    {
        $today = Carbon::today();
        $this->info("Running deadline notifications for: " . $today->toDateString());

        // --force: wipe today's sent records so every notification is re-sent
        if ($this->option('force')) {
            $deleted = DB::table('deadline_notifications')
                ->whereDate('created_at', $today)
                ->delete();
            $this->warn("--force: cleared {$deleted} sent record(s) for today. All notifications will be re-sent.");
            Log::info('[DeadlineNotify] --force flag: cleared today\'s records', [
                'deleted' => $deleted, 'date' => $today->toDateString(),
            ]);
        }

        $recipients = $this->getDeadlineRecipients();

        if ($recipients->isEmpty()) {
            $this->warn('No recipients found (Accounts Book / active in po_email_tbl). Aborting.');
            return Command::SUCCESS;
        }

        $this->info('Recipients (unique email/mobile from Email Master): ' . $recipients->count());

        $this->checkBills($today, $recipients);
        $this->checkBillTimelines($today, $recipients);
        $this->checkPurchaseOrders($today, $recipients);
        $this->checkPurchaseOrderTimelines($today, $recipients);
        $this->checkQuotations($today, $recipients);
        $this->checkQuotationTimelines($today, $recipients);

        $this->info('Done.');
        return Command::SUCCESS;
    }

    /**
     * Recipients from Email Master: active (status=1) and Accounts Book in menu_type (JSON or plain),
     * same pattern as purchase approval / Radiant (filtered menu + fallback).
     */
    private function getDeadlineRecipients(): Collection
    {
        $configs = TblPoEmail::query()
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('menu_type', 'like', '%Accounts Book%')
                    ->orWhere('menu_type', 'Accounts Book');
            })
            ->get();

        if ($configs->isEmpty()) {
            Log::warning('[DeadlineNotify] No po_email_tbl rows for Accounts Book; falling back to all active (status=1)');
            $configs = TblPoEmail::query()->where('status', 1)->get();
        }

        $rows = [];
        foreach ($configs as $r) {
            $email  = trim((string) ($r->to_email ?: $r->email));
            $mobile = trim((string) ($r->mobile_number ?? ''));
            if ($email === '' && $mobile === '') {
                continue;
            }
            $normMobile = preg_replace('/\D+/', '', $mobile);
            $key        = strtolower($email) . '|' . $normMobile;
            if (isset($rows[$key])) {
                continue;
            }
            $rows[$key] = (object) [
                'email'         => $email !== '' ? $email : null,
                'mobile_number' => $mobile !== '' ? $mobile : null,
                'created_by'    => $r->created_by ?? 'Team',
            ];
        }

        return collect(array_values($rows));
    }

    private function checkBills(Carbon $today, $recipients): void
    {
        // $bills = DB::table('bill_tbl')
        //     ->where('delete_status', 0)
        //     ->whereNotIn('bill_status', ['paid', 'cancelled'])
        //     ->whereRaw("
        //         STR_TO_DATE(due_date, '%d/%m/%Y') = ?
        //         OR (due_date IS NOT NULL AND due_date != '' AND CAST(due_date AS DATE) <= ?)
        //     ", [$today, $today])
        //     ->select('id', 'bill_gen_number', 'bill_number', 'vendor_name', 'due_date',
        //              'grand_total_amount', 'bill_status')
        //     ->get();

        $bills = DB::table('bill_tbl')
                ->where('delete_status', 0)
                ->whereNotIn('bill_status', ['paid', 'cancelled'])
                ->where(function ($query) use ($today) {
                    $query->whereRaw("STR_TO_DATE(due_date, '%d/%m/%Y') = ?", [$today])
                        ->orWhereDate('due_date', $today);
                })
                ->select(
                    'id',
                    'bill_gen_number',
                    'bill_number',
                    'vendor_name',
                    'due_date',
                    'grand_total_amount',
                    'bill_status'
                )
                ->get();

        foreach ($bills as $bill) {
            $dueDate = $this->parseDate($bill->due_date);
            if (!$dueDate) continue;

            $dueStatus = $dueDate->isToday() ? 'due_today' : 'overdue';
            $label     = $dueDate->isToday()
                ? "due TODAY ({$dueDate->format('d/m/Y')})"
                : "OVERDUE since {$dueDate->format('d/m/Y')} ({$dueDate->diffInDays($today)} days ago)";

            $number  = $bill->bill_gen_number ?? $bill->bill_number ?? "ID#{$bill->id}";
            $vendor  = $bill->vendor_name ?? 'Vendor';
            $amount  = number_format((float)($bill->grand_total_amount ?? 0), 2);

            $smsMsg = $this->compactSmsLine('bill', $number, $dueStatus);

            // FIX: compute label strings BEFORE putting them in double-quoted strings
            $emailSubLabel = ($dueStatus === 'due_today') ? 'Due Today' : 'OVERDUE';
            $emailSub      = "Bill {$emailSubLabel}: {$number}";

            $emailBody = $this->buildEmailBody('Bill', $number, $vendor, $amount, $label, 'bill');

            $webTitleLabel = ($dueStatus === 'due_today') ? 'Due Today' : 'Overdue';
            $webTitle      = "Bill {$webTitleLabel}: {$number}";
            $webMsg        = "Bill {$number} ({$vendor}) - Rs.{$amount} - {$label}";

            $this->dispatchNotifications('bill', $bill->id, $number, $dueDate, $dueStatus,
                $smsMsg, $emailSub, $emailBody, $webTitle, $webMsg, $recipients);
        }

        $this->info("Bills checked: {$bills->count()}");
    }

    /** bill_tbl.timeline_date is a SQL DATE column — notify when it is today. */
    private function checkBillTimelines(Carbon $today, $recipients): void
    {
        $bills = DB::table('bill_tbl')
            ->where('delete_status', 0)
            ->whereNotIn('bill_status', ['paid', 'cancelled'])
            ->whereNotNull('timeline_date')
            ->whereDate('timeline_date', $today)
            ->select(
                'id',
                'bill_gen_number',
                'bill_number',
                'vendor_name',
                'timeline_date',
                'grand_total_amount',
                'bill_status'
            )
            ->get();

        foreach ($bills as $bill) {
            $tlDate = $this->parseNativeDateColumn($bill->timeline_date);
            if (! $tlDate || ! $tlDate->isSameDay($today)) {
                continue;
            }

            $dueStatus = 'timeline_today';
            $label     = "Timeline ends TODAY ({$tlDate->format('d/m/Y')})";

            $number = $bill->bill_gen_number ?? $bill->bill_number ?? "ID#{$bill->id}";
            $vendor = $bill->vendor_name ?? 'Vendor';
            $amount = number_format((float) ($bill->grand_total_amount ?? 0), 2);

            $smsMsg = $this->compactSmsLine('bill', $number, 'timeline_today');

            $emailSub = "Bill timeline ends today: {$number}";
            $emailBody = $this->buildEmailBody('Bill', $number, $vendor, $amount, $label, 'bill_timeline');

            $webTitle = "Bill timeline: {$number}";
            $webMsg   = "Bill {$number} ({$vendor}) — {$label} — Rs.{$amount}";

            $this->dispatchNotifications('bill_timeline', $bill->id, $number, $tlDate, $dueStatus,
                $smsMsg, $emailSub, $emailBody, $webTitle, $webMsg, $recipients);
        }

        $this->info("Bill timelines (end today): {$bills->count()}");
    }

    private function checkPurchaseOrders(Carbon $today, $recipients): void
    {
        // $pos = DB::table('purchase_order_tbl')
        //     ->where('delete_status', 0)
        //     ->whereNotIn('status', ['received', 'cancelled', 'closed'])
        //     ->whereRaw("
        //         STR_TO_DATE(COALESCE(due_date), '%d/%m/%Y') <= ?
        //         OR CAST(COALESCE(due_date) AS DATE) <= ?
        //     ", [$today, $today])
        //     ->select('id', 'purchase_gen_order', 'vendor_name', 'due_date', 'grand_total_amount', 'status')
        //     ->get();

        $pos = DB::table('purchase_order_tbl')
                ->where('delete_status', 0)
                ->whereNotIn('status', ['received', 'cancelled', 'closed'])
                ->where(function ($query) use ($today) {
                    $query->whereRaw("STR_TO_DATE(due_date, '%d/%m/%Y') = ?", [$today])
                        ->orWhereDate('due_date', $today);
                })
                ->select(
                    'id',
                    'purchase_gen_order',
                    'vendor_name',
                    'due_date',
                    'grand_total_amount',
                    'status'
                )
                ->get();

        foreach ($pos as $po) {
            $rawDate = $po->due_date;
            $dueDate = $this->parseDate($rawDate);
            if (!$dueDate) continue;

            $dueStatus = $dueDate->isToday() ? 'due_today' : 'overdue';
            $label     = $dueDate->isToday()
                ? "due TODAY ({$dueDate->format('d/m/Y')})"
                : "OVERDUE since {$dueDate->format('d/m/Y')} ({$dueDate->diffInDays($today)} days ago)";

            $number  = $po->purchase_gen_order ?? "PO-ID#{$po->id}";
            $vendor  = $po->vendor_name ?? 'Vendor';
            $amount  = number_format((float)($po->grand_total_amount ?? 0), 2);

            $smsMsg = $this->compactSmsLine('po', $number, $dueStatus);

            // FIX: compute label strings BEFORE putting them in double-quoted strings
            $emailSubLabel = ($dueStatus === 'due_today') ? 'Due Today' : 'OVERDUE';
            $emailSub      = "Purchase Order {$emailSubLabel}: {$number}";

            $emailBody = $this->buildEmailBody('Purchase Order', $number, $vendor, $amount, $label, 'po');

            $webTitleLabel = ($dueStatus === 'due_today') ? 'Due Today' : 'Overdue';
            $webTitle      = "PO {$webTitleLabel}: {$number}";
            $webMsg        = "PO {$number} ({$vendor}) - Rs.{$amount} - {$label}";

            $this->dispatchNotifications('po', $po->id, $number, $dueDate, $dueStatus,
                $smsMsg, $emailSub, $emailBody, $webTitle, $webMsg, $recipients);
        }

        $this->info("POs checked: {$pos->count()}");
    }

    /** purchase_order_tbl.timeline_date is stored like due_date (often d/m/Y). */
    private function checkPurchaseOrderTimelines(Carbon $today, $recipients): void
    {
        $pos = DB::table('purchase_order_tbl')
            ->where('delete_status', 0)
            ->whereNotIn('status', ['received', 'cancelled', 'closed'])
            ->whereNotNull('timeline_date')
            ->where('timeline_date', '!=', '')
            ->where(function ($query) use ($today) {
                $query->whereRaw("STR_TO_DATE(timeline_date, '%d/%m/%Y') = ?", [$today])
                    ->orWhereDate('timeline_date', $today);
            })
            ->select(
                'id',
                'purchase_gen_order',
                'vendor_name',
                'timeline_date',
                'grand_total_amount',
                'status'
            )
            ->get();

        foreach ($pos as $po) {
            $tlDate = $this->parseDate($po->timeline_date);
            if (! $tlDate || ! $tlDate->isSameDay($today)) {
                continue;
            }

            $dueStatus = 'timeline_today';
            $label     = "Timeline ends TODAY ({$tlDate->format('d/m/Y')})";

            $number = $po->purchase_gen_order ?? "PO-ID#{$po->id}";
            $vendor = $po->vendor_name ?? 'Vendor';
            $amount = number_format((float) ($po->grand_total_amount ?? 0), 2);

            $smsMsg = $this->compactSmsLine('po', $number, 'timeline_today');

            $emailSub = "PO timeline ends today: {$number}";
            $emailBody = $this->buildEmailBody('Purchase Order', $number, $vendor, $amount, $label, 'po_timeline');

            $webTitle = "PO timeline: {$number}";
            $webMsg   = "PO {$number} ({$vendor}) — {$label} — Rs.{$amount}";

            $this->dispatchNotifications('po_timeline', $po->id, $number, $tlDate, $dueStatus,
                $smsMsg, $emailSub, $emailBody, $webTitle, $webMsg, $recipients);
        }

        $this->info("PO timelines (end today): {$pos->count()}");
    }

    private function checkQuotations(Carbon $today, $recipients): void
    {
        // $quotes = DB::table('quotation_order_tbl')
        //     ->where('delete_status', 0)
        //     ->whereNotIn('status', ['approved', 'rejected', 'cancelled'])
        //     ->whereRaw("
        //         STR_TO_DATE(COALESCE(due_date), '%d/%m/%Y') <= ?
        //         OR CAST(COALESCE(due_date) AS DATE) <= ?
        //     ", [$today, $today])
        //     ->select('id', 'quotation_gen_no', 'vendor_name', 'due_date', 'grand_total_amount', 'status')
        //     ->get();
        $quotes = DB::table('quotation_order_tbl')
                ->where('delete_status', 0)
                ->whereNotIn('status', ['approved', 'rejected', 'cancelled'])
                ->where(function ($query) use ($today) {
                    $query->whereRaw("STR_TO_DATE(due_date, '%d/%m/%Y') = ?", [$today])
                        ->orWhereDate('due_date', $today);
                })
                ->select(
                    'id',
                    'quotation_gen_no',
                    'vendor_name',
                    'due_date',
                    'grand_total_amount',
                    'status'
                )
                ->get();

        foreach ($quotes as $q) {
            $rawDate = $q->due_date;
            $dueDate = $this->parseDate($rawDate);
            if (!$dueDate) continue;

            $dueStatus = $dueDate->isToday() ? 'due_today' : 'overdue';
            $label     = $dueDate->isToday()
                ? "due TODAY ({$dueDate->format('d/m/Y')})"
                : "OVERDUE since {$dueDate->format('d/m/Y')} ({$dueDate->diffInDays($today)} days ago)";

            $number  = $q->quotation_gen_no ?? "QT-ID#{$q->id}";
            $vendor  = $q->vendor_name ?? 'Vendor';
            $amount  = number_format((float)($q->grand_total_amount ?? 0), 2);

            $smsMsg = $this->compactSmsLine('quotation', $number, $dueStatus);

            // FIX: compute label strings BEFORE putting them in double-quoted strings
            $emailSubLabel = ($dueStatus === 'due_today') ? 'Due Today' : 'OVERDUE';
            $emailSub      = "Quotation {$emailSubLabel}: {$number}";

            $emailBody = $this->buildEmailBody('Quotation', $number, $vendor, $amount, $label, 'quotation');

            $webTitleLabel = ($dueStatus === 'due_today') ? 'Due Today' : 'Overdue';
            $webTitle      = "Quotation {$webTitleLabel}: {$number}";
            $webMsg        = "Quotation {$number} ({$vendor}) - Rs.{$amount} - {$label}";

            $this->dispatchNotifications('quotation', $q->id, $number, $dueDate, $dueStatus,
                $smsMsg, $emailSub, $emailBody, $webTitle, $webMsg, $recipients);
        }

        $this->info("Quotations checked: {$quotes->count()}");
    }

    private function checkQuotationTimelines(Carbon $today, $recipients): void
    {
        $quotes = DB::table('quotation_order_tbl')
            ->where('delete_status', 0)
            ->whereNotIn('status', ['approved', 'rejected', 'cancelled'])
            ->whereNotNull('timeline_date')
            ->where('timeline_date', '!=', '')
            ->where(function ($query) use ($today) {
                $query->whereRaw("STR_TO_DATE(timeline_date, '%d/%m/%Y') = ?", [$today])
                    ->orWhereDate('timeline_date', $today);
            })
            ->select(
                'id',
                'quotation_gen_no',
                'vendor_name',
                'timeline_date',
                'grand_total_amount',
                'status'
            )
            ->get();

        foreach ($quotes as $q) {
            $tlDate = $this->parseDate($q->timeline_date);
            if (! $tlDate || ! $tlDate->isSameDay($today)) {
                continue;
            }

            $dueStatus = 'timeline_today';
            $label     = "Timeline ends TODAY ({$tlDate->format('d/m/Y')})";

            $number = $q->quotation_gen_no ?? "QT-ID#{$q->id}";
            $vendor = $q->vendor_name ?? 'Vendor';
            $amount = number_format((float) ($q->grand_total_amount ?? 0), 2);

            $smsMsg = $this->compactSmsLine('quotation', $number, 'timeline_today');

            $emailSub = "Quotation timeline ends today: {$number}";
            $emailBody = $this->buildEmailBody('Quotation', $number, $vendor, $amount, $label, 'quotation_timeline');

            $webTitle = "Quotation timeline: {$number}";
            $webMsg   = "Quotation {$number} ({$vendor}) — {$label} — Rs.{$amount}";

            $this->dispatchNotifications('quotation_timeline', $q->id, $number, $tlDate, $dueStatus,
                $smsMsg, $emailSub, $emailBody, $webTitle, $webMsg, $recipients);
        }

        $this->info("Quotation timelines (end today): {$quotes->count()}");
    }

    private function dispatchNotifications(
        string $type,
        int    $recordId,
        string $number,
        Carbon $dueDate,
        string $dueStatus,
        string $smsMsg,
        string $emailSub,
        string $emailBody,
        string $webTitle,
        string $webMsg,
               $recipients
    ): void {
        foreach ($recipients as $recipient) {
            $email  = $recipient->email ?? $recipient->to_email ?? null;
            $mobile = $recipient->mobile_number ?? null;
            $name   = $recipient->created_by ?? 'Team';

            if ($mobile) {
                if ($this->alreadySent($type, $recordId, 'sms', $dueStatus)) {
                    $this->line("  [SKIP] SMS already sent to {$mobile} [{$type} {$number}]");
                } else {
                    $this->sendSms($mobile, $name, $smsMsg, $type, $recordId, $number, $dueDate, $dueStatus);
                }
            }

            if ($email) {
                if ($this->alreadySent($type, $recordId, 'email', $dueStatus)) {
                    $this->line("  [SKIP] Email already sent to {$email} [{$type} {$number}]");
                } else {
                    $this->sendEmail($email, $name, $emailSub, $emailBody, $type, $recordId, $number, $dueDate, $dueStatus);
                }
            }
        }

        if ($this->alreadySent($type, $recordId, 'web', $dueStatus)) {
            $this->line("  [SKIP] Web notification already sent [{$type} {$number}]");
        } else {
            $this->createWebNotification($type, $recordId, $number, $webTitle, $webMsg, $dueDate, $dueStatus);
        }
    }

    /**
     * Short text for the single DLT variable after "Dear " (bill/PO/QT id + status).
     * Example: "DAP-BILL-040 overdue amount", "PO GEN-1 timeline end amount".
     */
    private function compactSmsLine(string $recordType, string $number, string $dueStatus): string
    {
        $n = trim($number);
        $prefix = match ($recordType) {
            'po' => 'PO ',
            'quotation' => 'QT ',
            default => '',
        };

        $state = match ($dueStatus) {
            'timeline_today' => 'timeline end amount',
            'due_today' => 'due today',
            'overdue' => 'overdue amount',
            default => 'alert',
        };

        return trim("{$prefix}{$n} {$state}");
    }

    private function sendSms(
        string $mobile, string $name, string $detailMsg,
        string $type, int $recordId, string $number,
        Carbon $dueDate, string $dueStatus
    ): void {
        $smsSvc = app(HmsDltPortalSmsService::class);
        $result = $smsSvc->send($mobile, $name, $detailMsg, 'deadline_notify');
        $gatewayOk = $result['success'];
        $responseStr = $result['response'] ?? '';
        $curlErr = $result['curl_error'] ?? '';
        $failDetail = $curlErr !== '' ? $curlErr : (! $gatewayOk ? $responseStr : null);

        Log::info("[DeadlineNotify][SMS] Dispatch to {$mobile}", [
            'type' => $type,
            'number' => $number,
            'dlt_var' => $result['greeting_var'] ?? '',
            'template_msg' => $result['template_msg'] ?? '',
        ]);

        try {
            DB::table('deadline_notifications')->insert([
                'type'             => $type,
                'record_id'        => $recordId,
                'record_number'    => $number,
                'recipient_mobile' => $mobile,
                'recipient_name'   => $name,
                'channel'          => 'sms',
                'status'           => $gatewayOk ? 'sent' : 'failed',
                'message'          => $detailMsg,
                'error'            => $failDetail,
                'due_date'         => $dueDate->toDateString(),
                'due_status'       => $dueStatus,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("[DeadlineNotify][SMS] DB insert failed", [
                'error' => $e->getMessage(),
            ]);
        }

        $this->line('  SMS to ' . $mobile . " [{$type} {$number}] "
            . ($gatewayOk ? 'sent (response: ' . $responseStr . ')' : 'FAILED: ' . ($failDetail ?: 'unknown')));
    }

    private function sendEmail(
        string $email, string $name, string $subject, string $htmlBody,
        string $type, int $recordId, string $number,
        Carbon $dueDate, string $dueStatus
    ): void {
        $success = false;
        $error   = null;

        try {
            Mail::html($htmlBody, function ($msg) use ($email, $name, $subject) {
                $msg->to($email, $name)
                    ->subject($subject)
                    ->from(
                        config('mail.from.address', 'noreply@draravindsivf.com'),
                        config('mail.from.name', "Dr. Aravind's IVF")
                    );
            });
            $success = true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error("DeadlineNotification email failed: {$error}");
        }

        try {
            DB::table('deadline_notifications')->insert([
                'type'            => $type,
                'record_id'       => $recordId,
                'record_number'   => $number,
                'recipient_email' => $email,
                'recipient_name'  => $name,
                'channel'         => 'email',
                'status'          => $success ? 'sent' : 'failed',
                'message'         => $subject,
                'error'           => $error,
                'due_date'        => $dueDate->toDateString(),
                'due_status'      => $dueStatus,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("[DeadlineNotify][Email] deadline_notifications insert FAILED", [
                'error' => $e->getMessage(),
            ]);
        }

        $this->line("  Email to {$email} [{$type} {$number}] " . ($success ? 'sent' : 'FAILED: ' . $error));
    }

    private function createWebNotification(
        string $type, int $recordId, string $number,
        string $title, string $message,
        Carbon $dueDate, string $dueStatus
    ): void {
        $urlMap = [
            'bill'                => "/superadmin/bill_dashboard?id={$recordId}",
            'bill_timeline'       => "/superadmin/bill_dashboard?id={$recordId}",
            'po'                  => "/superadmin/purchase_dashboard?id={$recordId}",
            'po_timeline'         => "/superadmin/purchase_dashboard?id={$recordId}",
            'quotation'           => "/superadmin/quotation_dashboard?id={$recordId}",
            'quotation_timeline'  => "/superadmin/quotation_dashboard?id={$recordId}",
        ];

        Log::info("[DeadlineNotify][Web] Creating notification", [
            'type' => $type, 'number' => $number, 'title' => $title,
        ]);

        // Insert into web_notifications (shown in the bell icon)
        try {
            DB::table('web_notifications')->insert([
                'user_id'       => null,
                'title'         => $title,
                'message'       => $message,
                'type'          => $type,
                'record_id'     => $recordId,
                'record_number' => $number,
                'url'           => $urlMap[$type] ?? '#',
                'is_read'       => 0,
                'due_date'      => $dueDate->toDateString(),
                'due_status'    => $dueStatus,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            Log::info("[DeadlineNotify][Web] web_notifications row inserted OK", [
                'type' => $type, 'number' => $number,
            ]);
        } catch (\Exception $e) {
            Log::error("[DeadlineNotify][Web] web_notifications insert FAILED", [
                'error' => $e->getMessage(), 'trace' => $e->getTraceAsString(),
            ]);
            $this->error("  Web notification DB insert failed: " . $e->getMessage());
        }

        // Record that we sent a web notification (for deduplication)
        try {
            DB::table('deadline_notifications')->insert([
                'type'          => $type,
                'record_id'     => $recordId,
                'record_number' => $number,
                'channel'       => 'web',
                'status'        => 'sent',
                'message'       => $message,
                'due_date'      => $dueDate->toDateString(),
                'due_status'    => $dueStatus,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("[DeadlineNotify][Web] deadline_notifications insert FAILED", [
                'error' => $e->getMessage(),
            ]);
        }

        $this->line("  Web notification [{$type} {$number}] created");
    }

    private function alreadySent(string $type, int $recordId, string $channel, string $dueStatus): bool
    {
        return DB::table('deadline_notifications')
            ->where('type',       $type)
            ->where('record_id',  $recordId)
            ->where('channel',    $channel)
            ->where('due_status', $dueStatus)
            ->where('status',     'sent')
            ->exists();
    }

    private function parseDate($value): ?Carbon
    {
        if (empty($value)) return null;
        $value = trim($value);
        try { return Carbon::createFromFormat('d/m/Y', $value); } catch (\Exception $e) {}
        try { return Carbon::parse($value); }                    catch (\Exception $e) {}
        return null;
    }

    /** For MySQL DATE / datetime columns (e.g. bill_tbl.timeline_date). */
    private function parseNativeDateColumn($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function buildEmailBody(
        string $docType, string $number, string $vendor,
        string $amount,  string $label,  string $type
    ): string {
        $color = str_contains($label, 'OVERDUE') ? '#dc2626' : '#d97706';
        $icon  = str_contains($label, 'OVERDUE') ? 'OVERDUE' : 'DUE TODAY';

        return '<!DOCTYPE html>
<html>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0;">
  <div style="max-width:600px;margin:32px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);">
    <div style="background:#1e293b;padding:24px 32px;">
      <h1 style="color:#fff;margin:0;font-size:20px;">Dr. Aravind\'s IVF — ' . $docType . ' Alert [' . $icon . ']</h1>
    </div>
    <div style="padding:28px 32px;">
      <p style="font-size:16px;color:#334155;margin:0 0 20px;">
        The following <strong>' . $docType . '</strong> requires your attention:
      </p>
      <table style="width:100%;border-collapse:collapse;margin-bottom:24px;">
        <tr style="background:#f8fafc;">
          <td style="padding:10px 14px;font-weight:600;color:#475569;width:40%;">' . $docType . ' Number</td>
          <td style="padding:10px 14px;color:#1e293b;"><strong>' . $number . '</strong></td>
        </tr>
        <tr>
          <td style="padding:10px 14px;font-weight:600;color:#475569;">Vendor</td>
          <td style="padding:10px 14px;color:#1e293b;">' . $vendor . '</td>
        </tr>
        <tr style="background:#f8fafc;">
          <td style="padding:10px 14px;font-weight:600;color:#475569;">Amount</td>
          <td style="padding:10px 14px;color:#1e293b;">Rs. ' . $amount . '</td>
        </tr>
        <tr>
          <td style="padding:10px 14px;font-weight:600;color:#475569;">Status</td>
          <td style="padding:10px 14px;color:' . $color . ';font-weight:700;">' . $label . '</td>
        </tr>
      </table>
      <a href="https://draravinds.com/hms"
         style="display:inline-block;background:#4f46e5;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;">
        Login to HMS Portal
      </a>
    </div>
    <div style="background:#f8fafc;padding:16px 32px;border-top:1px solid #e2e8f0;">
      <p style="font-size:12px;color:#94a3b8;margin:0;">
        This is an automated notification from Dr. Aravind\'s IVF HRMS system. Please do not reply.
      </p>
    </div>
  </div>
</body>
</html>';
    }
}