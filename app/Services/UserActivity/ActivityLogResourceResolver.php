<?php

namespace App\Services\UserActivity;

use App\Models\TblQuotation;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Builds a short reference and optional in-app URL from log path, route, query, and
 * a small whitelisted request snapshot (POST/GET) captured for each activity row.
 */
class ActivityLogResourceResolver
{
    public function resolveForApi(UserActivityLog $e): array
    {
        $route = (string) ($e->route_name ?? '');
        $q = $this->parseQueryString($e->url_query ?? null);
        $snap = is_array($e->request_snapshot) ? $e->request_snapshot : [];
        if (! is_array($snap)) {
            $snap = [];
        }

        $r = $this->resolve($route, (string) ($e->path ?? ''), $q, $snap);

        return array_merge(
            [
                'reference' => '—',
                'link_url' => null,
                'link_label' => 'Open',
            ],
            $r
        );
    }

    private function parseQueryString(?string $s): array
    {
        if ($s === null || trim($s) === '') {
            return [];
        }
        $out = [];
        parse_str($s, $out);

        return is_array($out) ? $out : [];
    }

    private function intOrNull(mixed $v): ?int
    {
        if (is_int($v)) {
            return $v > 0 ? $v : null;
        }
        if (is_string($v) && ctype_digit($v)) {
            $i = (int) $v;

            return $i > 0 ? $i : null;
        }

        return null;
    }

    /**
     * For logs saved before the saved record id was merged into request_snapshot.
     */
    private function lookupQuotationIdByQuotationNo(string $qNo): ?int
    {
        if ($qNo === '') {
            return null;
        }
        $id = TblQuotation::query()
            ->where('delete_status', 0)
            ->where('quotation_no', $qNo)
            ->orderByDesc('id')
            ->value('id');
        if ($id === null) {
            return null;
        }
        $i = (int) $id;

        return $i > 0 ? $i : null;
    }

    private function str(mixed $v, int $max = 64): string
    {
        $s = is_string($v) || is_scalar($v) ? trim((string) $v) : '';

        return $s === '' ? '' : Str::limit($s, $max, '…');
    }

    private function toUrlIfRouteExists(string $name, array $query = []): ?string
    {
        if (! Route::has($name)) {
            return null;
        }
        try {
            $u = rtrim((string) route($name, [], true), '/');
            if (count($query) === 0) {
                return $u;
            }
            $sep = str_contains($u, '?') ? '&' : '?';

            return $u.$sep.http_build_query($query);
        } catch (\Throwable) {
            return null;
        }
    }

    private function namedUrlOrPath(string $routeName, int $id, string $pathFallback): string
    {
        if (Route::has($routeName)) {
            $base = rtrim((string) route($routeName, [], false), '/');

            return rtrim($base, '/').'?'.http_build_query(['id' => $id]);
        }
        if ($pathFallback !== '') {
            $p = str_starts_with($pathFallback, '/')
                ? $pathFallback
                : '/'.ltrim($pathFallback, '/');

            return rtrim((string) url($p), '/').'?'.http_build_query(['id' => $id]);
        }

        return (string) url('/').'?'.http_build_query(['id' => $id]);
    }

    /**
     * @param  array<string, mixed>  $q
     * @param  array<string, mixed>  $snap
     * @return array{reference: string, link_url: string|null, link_label: string}
     */
    private function resolve(string $route, string $path, array $q, array $snap): array
    {
        $qId = $this->intOrNull($q['id'] ?? null) ?? $this->intOrNull($q['bill_id'] ?? null) ?? $this->intOrNull($q['ticket_id'] ?? null) ?? $this->intOrNull($q['petty_cash_id'] ?? null);
        $genericSnapId = $this->intOrNull($snap['id'] ?? null) ?? $this->intOrNull($snap['bill_id'] ?? null) ?? $this->intOrNull($snap['quotation_id'] ?? null) ?? $this->intOrNull($snap['purchase_id'] ?? null);
        $id = $genericSnapId ?? $qId;
        $billRowId = $this->intOrNull($snap['id'] ?? null) ?? $this->intOrNull($snap['bill_id'] ?? null) ?? $this->intOrNull($qId);
        $poRowId = $this->intOrNull($snap['id'] ?? null) ?? $this->intOrNull($snap['purchase_id'] ?? null) ?? $this->intOrNull($qId);
        $billNo = $this->str($snap['bill_number'] ?? '', 32);
        $qNo = $this->str($snap['quotation_number'] ?? $snap['q_no'] ?? $snap['quotation_no'] ?? '', 32);
        $qRowId = $this->intOrNull($snap['id'] ?? null) ?? $this->intOrNull($snap['quotation_id'] ?? null) ?? $qId;
        if (! $qRowId && $qNo !== '') {
            $qRowId = $this->lookupQuotationIdByQuotationNo($qNo);
        }
        $poNo = $this->str($snap['order_number'] ?? $snap['purchase_order_number'] ?? $snap['po_number'] ?? '', 32);
        $ticketNo = $this->str($snap['ticket_no'] ?? $snap['ticket_code'] ?? '', 32);

        // ——— Vendor bill (print) GET ———
        if ($route === 'superadmin.getbillprint' || $route === 'superadmin.getbillpdf') {
            $bid = $this->intOrNull($q['id'] ?? null) ?? $this->intOrNull($qId);
            if ($bid) {
                return [
                    'reference' => 'Bill (id '.(string) $bid.')',
                    'link_url' => $this->namedUrlOrPath('superadmin.getbillprint', (int) $bid, 'superadmin/bill_print'),
                    'link_label' => 'View bill PDF',
                ];
            }
        }

        if ($route === 'superadmin.savebill') {
            if ($billRowId) {
                return [
                    'reference' => $billNo !== '' ? "Bill: {$billNo} (id {$billRowId})" : "Bill (id {$billRowId})",
                    'link_url' => $this->namedUrlOrPath('superadmin.getbillprint', (int) $billRowId, 'superadmin/bill_print'),
                    'link_label' => 'View bill',
                ];
            }
            if ($billNo !== '') {
                $list = $this->toUrlIfRouteExists('superadmin.getbill', []) ?? (string) url('superadmin/bill_dashboard');

                return [
                    'reference' => "Bill: {$billNo}",
                    'link_url' => $list,
                    'link_label' => 'Bills',
                ];
            }

            return [
                'reference' => 'Bill (new save)',
                'link_url' => $this->toUrlIfRouteExists('superadmin.getbill', []),
                'link_label' => 'Bill list',
            ];
        }

        if ($route === 'superadmin.savebillmade') {
            $bm = $this->intOrNull($snap['id'] ?? null) ?? $this->intOrNull($qId);
            if ($bm) {
                return [
                    'reference' => "Bill made (id {$bm})",
                    'link_url' => $this->namedUrlOrPath('superadmin.getbillmadeprint', (int) $bm, 'superadmin/bill_made_print'),
                    'link_label' => 'View',
                ];
            }

            return [
                'reference' => 'Bill made (save)',
                'link_url' => $this->toUrlIfRouteExists('superadmin.getbillmade', []),
                'link_label' => 'List',
            ];
        }

        // ——— Quotation ———
        if ($route === 'superadmin.getquotationprint' || $route === 'superadmin.getquotationpdf') {
            $bid = $this->intOrNull($q['id'] ?? $qId);
            if ($bid) {
                return [
                    'reference' => 'Quotation #'.(string) $bid,
                    'link_url' => $this->namedUrlOrPath('superadmin.getquotationprint', $bid, 'superadmin/quotation_print'),
                    'link_label' => 'View quotation PDF',
                ];
            }
        }
        if ($route === 'superadmin.savequotation' || $route === 'import.quotation') {
            if ($qRowId) {
                return [
                    'reference' => $qNo !== '' ? "Quotation: {$qNo} (id {$qRowId})" : "Quotation (id {$qRowId})",
                    'link_url' => $this->namedUrlOrPath('superadmin.getquotationcreate', (int) $qRowId, 'superadmin/quotation_create'),
                    'link_label' => 'Open quotation',
                ];
            }
            if ($qNo !== '') {
                return [
                    'reference' => "Quotation: {$qNo}",
                    'link_url' => $this->toUrlIfRouteExists('superadmin.getquotation', []),
                    'link_label' => 'Quotation list',
                ];
            }

            return [
                'reference' => 'Quotation (save)',
                'link_url' => $this->toUrlIfRouteExists('superadmin.getquotation', []),
                'link_label' => 'List',
            ];
        }

        // ——— Purchase order ———
        if ($route === 'superadmin.getpurchaseprint' || $route === 'superadmin.getpurchasepdf') {
            $bid = $this->intOrNull($q['id'] ?? $qId);
            if ($bid) {
                return [
                    'reference' => 'PO #'.(string) $bid,
                    'link_url' => $this->namedUrlOrPath('superadmin.getpurchaseprint', $bid, 'superadmin/po_print'),
                    'link_label' => 'View PO PDF',
                ];
            }
        }
        if (in_array($route, ['superadmin.savepurchaseorder', 'import.importpurchaseExcel'], true)) {
            if ($poRowId) {
                return [
                    'reference' => $poNo !== '' ? "PO: {$poNo} (id {$poRowId})" : "PO (id {$poRowId})",
                    'link_url' => $this->namedUrlOrPath('superadmin.getpurchaseprint', (int) $poRowId, 'superadmin/po_print'),
                    'link_label' => 'View PO',
                ];
            }
            if ($poNo !== '') {
                return [
                    'reference' => "PO: {$poNo}",
                    'link_url' => $this->toUrlIfRouteExists('superadmin.getpurchaseorder', []),
                    'link_label' => 'PO list',
                ];
            }

            return [
                'reference' => 'PO (save)',
                'link_url' => $this->toUrlIfRouteExists('superadmin.getpurchaseorder', []),
                'link_label' => 'PO list',
            ];
        }

        if ($route === 'superadmin.tickets.store' || $route === 'superadmin.tickets.update' || $route === 'superadmin.tickets.status') {
            $tid = $this->intOrNull($snap['id'] ?? $snap['ticket_id'] ?? null);
            if ($tid) {
                $ref = $ticketNo !== '' ? "Ticket {$ticketNo}" : "Ticket #{$tid}";

                return [
                    'reference' => $ref,
                    'link_url' => $this->toUrlIfRouteExists('superadmin.tickets.index', []),
                    'link_label' => 'Tickets',
                ];
            }
        }

        if (in_array($route, ['superadmin.savepettycash', 'superadmin.savepettycashbulk'], true)) {
            $pc = $this->intOrNull($snap['id'] ?? $snap['petty_cash_id'] ?? null) ?? $this->intOrNull($qId);
            if ($pc) {
                return [
                    'reference' => "Petty cash (id {$pc})",
                    'link_url' => (string) url('superadmin/petty-cash?petty_cash_id='.(int) $pc),
                    'link_label' => 'View petty cash',
                ];
            }
        }

        if ($route === 'user_activity.client_event' || str_starts_with($path, 'client/')) {
            $page = $this->str($snap['page'] ?? '', 80);
            $ev = $this->str(ltrim((string) str_replace('client/', '', $path), '/'), 48);
            if ($ev === '') {
                $ev = 'ui';
            }
            $ref = $page !== '' ? "Tab / page ({$ev}) — {$page}" : "Browser: {$ev}";

            return [
                'reference' => $ref,
                'link_url' => ($page !== '' && (str_starts_with($page, '/') || str_starts_with($page, 'http')))
                    ? (str_starts_with($page, 'http') ? $page : (string) url($page))
                    : null,
                'link_label' => 'Open page',
            ];
        }

        // Generic: path has numeric id segment, e.g. /superadmin/purchase-flow/po/12
        if (preg_match('/\/(\d+)(?:\/|\?|$)/', $path, $m)) {
            $seg = (int) $m[1];
            if ($seg > 0) {
                return [
                    'reference' => "Record ".$seg,
                    'link_url' => null,
                    'link_label' => 'Open',
                ];
            }
        }

        $anyId = $id;
        if ($anyId) {
            return [
                'reference' => 'ID '.(string) (int) $anyId,
                'link_url' => null,
                'link_label' => 'Open',
            ];
        }

        if ($route !== '' && (str_ends_with($route, '.store') || str_ends_with($route, '.update'))) {
            $short = Str::afterLast($route, '.');
            $p = $path;
            if ($p !== '' && $p[0] !== '/') {
                $p = '/'.$p;
            }

            return [
                'reference' => ucfirst(str_replace('_', ' ', (string) Str::before($route, '.')).' · '.$short),
                'link_url' => $p !== '' ? (string) url($p) : null,
                'link_label' => 'Open route',
            ];
        }

        return [
            'reference' => '—',
            'link_url' => null,
            'link_label' => 'Open',
        ];
    }
}
