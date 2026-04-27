<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BillingListModel;
use App\Models\TblZonesModel;
use App\Models\TblLocationModel;
use App\Support\MocdocLocationKeys;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class BillingStatsController extends Controller
{
    /**
     * Main page  OR  AJAX JSON response
     */
    public function index(Request $request)
    {
        $admin = Auth::user();
        // Query param `ajax` (GET) — use input() so filters + ajax work reliably (not property magic)
        $ajaxKind = $request->query('ajax', $request->input('ajax'));

        // ── AJAX: stats only ──────────────────────────────────────
        if ($ajaxKind === 'stats') {
            return response()->json($this->buildStats($request));
        }

        // ── AJAX: table only ─────────────────────────────────────
        if ($ajaxKind === 'table') {
            return response()->json($this->buildTable($request));
        }

        // ── Full page load: pass dropdown options ─────────────
        // Zones and branches from master tables (same reference as VendorController)
        $zones    = TblZonesModel::orderBy('name')->get();
        $branches = TblLocationModel::orderBy('name')->get();

        $types        = BillingListModel::select('type')
                            ->distinct()->whereNotNull('type')->orderBy('type')->pluck('type');
        $paymentTypes = BillingListModel::select('paymenttype')
                            ->distinct()->whereNotNull('paymenttype')->orderBy('paymenttype')->pluck('paymenttype');

        return view('mocdoc_income.billing_stats', compact('zones', 'branches', 'types', 'paymentTypes', 'admin'));
    }

    // ─────────────────────────────────────────────────────────────
    // BUILD STATS  (payment cards, type breakdown, location list)
    // ─────────────────────────────────────────────────────────────
    private function buildStats(Request $request): array
    {
        $query = $this->baseQuery($request);

        $totalAmount  = (clone $query)->sum('amt');
        $totalRecords = (clone $query)->count();

        // Payment-type breakdown → keyed by paymenttype
        $paymentRaw = (clone $query)
            ->select('paymenttype', DB::raw('SUM(amt) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('paymenttype')
            ->get();

        $paymentStats = [];
        foreach ($paymentRaw as $row) {
            $paymentStats[$row->paymenttype] = [
                'total' => $row->total,
                'count' => $row->count,
            ];
        }

        // Type breakdown
        $typeStats = (clone $query)
            ->select('type', DB::raw('SUM(amt) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        // Location breakdown
        $locationStats = (clone $query)
            ->select('location_name', DB::raw('SUM(amt) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('location_name')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        return [
            'total_amount'   => $totalAmount,
            'total_records'  => $totalRecords,
            'payment_stats'  => $paymentStats,
            'type_stats'     => $typeStats,
            'location_stats' => $locationStats,
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // BUILD TABLE  (paginated rows)
    // ─────────────────────────────────────────────────────────────
    private function buildTable(Request $request): array
    {
        $query   = $this->baseQuery($request);
        $perPage = 20;
        $page    = max(1, (int) $request->get('page', 1));

        $total   = (clone $query)->count();
        $records = $query->orderByDesc('billdate')
                         ->offset(($page - 1) * $perPage)
                         ->limit($perPage)
                         ->get()
                         ->toArray();

        $lastPage = (int) ceil($total / $perPage);

        return [
            'records'    => $records,
            'total'      => $total,
            'from'       => $total ? (($page - 1) * $perPage) + 1 : 0,
            'to'         => min($page * $perPage, $total),
            'pagination' => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'total'        => $total,
                'per_page'     => $perPage,
            ],
        ];
    }

    /**
     * @return int[]
     */
    private function intArrayFromRequest(Request $request, string $key): array
    {
        $v = $request->input($key);
        if ($v === null || $v === '' || $v === []) {
            return [];
        }
        if (is_array($v)) {
            return array_values(array_unique(array_filter(array_map('intval', $v), static fn (int $x): bool => $x > 0)));
        }
        if (is_numeric($v)) {
            $n = (int) $v;

            return $n > 0 ? [$n] : [];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function stringArrayFromRequest(Request $request, string $key): array
    {
        $v = $request->input($key);
        if ($v === null || $v === '' || $v === []) {
            return [];
        }
        if (is_array($v)) {
            return array_values(array_filter(array_map('strval', $v), static fn (string $x): bool => $x !== ''));
        }

        return [(string) $v];
    }

    /**
     * @param  int[]  $tblLocationIds  tbl_locations.id
     */
    private function applyBillingLocationScope($query, array $tblLocationIds): void
    {
        if ($tblLocationIds === []) {
            $query->whereRaw('0 = 1');

            return;
        }
        $locations = TblLocationModel::whereIn('id', $tblLocationIds)->get();
        $variants = MocdocLocationKeys::billingLocationIdVariantsForBranches($locations);
        if ($variants === []) {
            $query->whereRaw('0 = 1');

            return;
        }
        $query->whereIn('location_id', $variants);
    }

    // ─────────────────────────────────────────────────────────────
    // BASE QUERY — shared filter logic
    // ─────────────────────────────────────────────────────────────
    private function baseQuery(Request $request)
    {
        $query = BillingListModel::query();

        // billdate: leading YYYYMMDD segment (compact API format) — string compare on first 8 digits
        if ($request->filled('date_from')) {
            try {
                $dateFrom = Carbon::parse($request->date_from)->format('Ymd');
                $query->whereRaw('LEFT(billdate, 8) >= ?', [$dateFrom]);
            } catch (\Exception $e) {
            }
        }

        if ($request->filled('date_to')) {
            try {
                $dateTo = Carbon::parse($request->date_to)->format('Ymd');
                $query->whereRaw('LEFT(billdate, 8) <= ?', [$dateTo]);
            } catch (\Exception $e) {
            }
        }

        // Zone → tbl_locations by zone_id → billing_list.location_id (API-style "location{N}")
        $zoneIds = $this->intArrayFromRequest($request, 'zone_ids');
        if ($zoneIds !== []) {
            $tblBranchIds = TblLocationModel::whereIn('zone_id', $zoneIds)
                ->pluck('id')
                ->map(static fn ($id): int => (int) $id)
                ->toArray();
            if (! empty($tblBranchIds)) {
                $this->applyBillingLocationScope($query, $tblBranchIds);
            } else {
                $query->whereRaw('0 = 1');
            }
        }

        // Branch → tbl_locations.id → same API key mapping (intersects with zone when both set)
        $branchIds = $this->intArrayFromRequest($request, 'branch_ids');
        if ($branchIds !== []) {
            $this->applyBillingLocationScope($query, $branchIds);
        }
        // Type multi-select
        $typeVals = $this->stringArrayFromRequest($request, 'type_vals');
        if ($typeVals !== []) {
            $query->whereIn('type', $typeVals);
        }
        // Payment type multi-select
        $paymentVals = $this->stringArrayFromRequest($request, 'payment_vals');
        if ($paymentVals !== []) {
            $query->whereIn('paymenttype', $paymentVals);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('patientname', 'like', "%$s%")
                  ->orWhere('billno',    'like', "%$s%")
                  ->orWhere('mobile',    'like', "%$s%")
                  ->orWhere('consultant','like', "%$s%");
            });
        }

        return $query;
    }

    // ─────────────────────────────────────────────────────────────
    // EXPORT (XLSX / CSV) — respects same filters
    // ─────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $records = $this->baseQuery($request)->orderByDesc('billdate')->get();
        $format  = $request->get('format', 'csv');

        $headers = [
            'ID','Location ID','Location Name','Type','Payment Type',
            'Amount','Bill No','Receipt No','Bill Date','Received At',
            'User Name','Patient Name','Gender','Age','Mobile',
            'Consultant','Referred By','OP No','Grand Total','Discount','Tax','Created At',
        ];

        $rows = $records->map(function ($r) {
            return [
                $r->id, $r->location_id, $r->location_name, $r->type, $r->paymenttype,
                $r->amt, $r->billno, $r->receiptno, $r->billdate, $r->receivedat,
                $r->user_name, $r->patientname, $r->gender, $r->age, $r->mobile,
                $r->consultant, $r->referredby, $r->opno, $r->grandtotal,
                $r->granddiscountvalue, $r->tax, $r->created_at,
            ];
        });

        return $format === 'xlsx'
            ? $this->toXlsx($headers, $rows)
            : $this->toCsv($headers, $rows);
    }

    private function toXlsx($headers, $rows)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet()->setTitle('Billing Data');
        $sheet       = $spreadsheet->getActiveSheet();

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        foreach ($headers as $col => $h) {
            $sheet->getCellByColumnAndRow($col + 1, 1)->setValue($h);
            $sheet->getStyleByColumnAndRow($col + 1, 1)->applyFromArray($headerStyle);
            $sheet->getColumnDimensionByColumn($col + 1)->setWidth(18);
        }

        foreach ($rows as $ri => $row) {
            foreach ($row as $ci => $val) {
                $sheet->getCellByColumnAndRow($ci + 1, $ri + 2)->setValue($val);
            }
            if ($ri % 2 === 0) {
                $sheet->getStyleByColumnAndRow(1, $ri+2, count($headers), $ri+2)
                      ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setRGB('F1F5F9');
            }
        }

        $sheet->setAutoFilter('A1:' . $sheet->getHighestColumn() . '1');

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'billing_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function toCsv($headers, $rows)
    {
        $filename = 'billing_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($headers, $rows) {
            $f = fopen('php://output', 'w');
            fputcsv($f, $headers);
            foreach ($rows as $row) {
                fputcsv($f, is_array($row) ? $row : $row->toArray());
            }
            fclose($f);
        };
        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // FETCH MISSED MOCDOC DATA  — call API + insert into billing_list
    // ─────────────────────────────────────────────────────────────
    public function fetchAndInsert(Request $request)
    {
        $request->validate([
            'date'      => 'required|date',
            'branch_id' => 'required|integer|exists:tbl_locations,id',
        ]);

        $date     = Carbon::parse($request->date)->format('Ymd');
        $branch   = TblLocationModel::find((int) $request->branch_id);
        if (! $branch) {
            return response()->json(['success' => false, 'message' => 'Branch not found.'], 404);
        }

        // Resolve the MocDoc location key (e.g. "location20") from the branch name
        $variants = MocdocLocationKeys::billingLocationIdVariantsForBranch($branch);
        $locationKey = collect($variants)->first(fn ($v) => str_starts_with((string) $v, 'location'));

        if (! $locationKey) {
            return response()->json([
                'success' => false,
                'message' => 'Branch "' . $branch->name . '" is not mapped to a MocDoc location key. Update MocdocLocationKeys.',
            ], 422);
        }

        $locName = MocdocLocationKeys::locationKeyToNameMap()[$locationKey] ?? $branch->name;

        // Call MocDoc API
        $response = $this->callMocdocApi(
            'https://mocdoc.in/api/get/billlist/draravinds-ivf',
            $date,
            $locationKey
        );

        if ($response === null) {
            return response()->json([
                'success' => false,
                'message' => 'MocDoc API call failed or returned no response. Check storage/logs/laravel.log for "BillingStats MocDoc".',
            ], 502);
        }

        $billingList = $this->extractMocdocBillingListFromDecoded($response);
        if (empty($billingList)) {
            \Log::warning('BillingStats MocDoc: empty billing list after successful HTTP response', [
                'context'       => 'fetchAndInsert',
                'date_ymd'      => $date,
                'location_key'  => $locationKey,
                'loc_name'      => $locName,
                'decoded_keys'  => is_array($response) ? array_keys($response) : null,
            ]);

            return response()->json([
                'success'  => true,
                'inserted' => 0,
                'skipped'  => 0,
                'total_api'=> 0,
                'message'  => 'No billing records returned from MocDoc for ' . $locName . ' on ' . Carbon::parse($request->date)->format('d M Y') . '. Nothing to insert.',
                'log_hint' => 'If Postman returns data for the same date/location, check laravel.log for "BillingStats MocDoc" (timeouts, JSON keys, or response snippet).',
            ]);
        }

        $inserted = 0;
        $skipped  = 0;
        $errors   = 0;
        $dateFmt  = Carbon::parse($request->date)->format('Ymd');

        foreach ($billingList as $item) {
            try {
                $billNo    = $item['billno'] ?? null;
                $receiptNo = $item['receiptno'] ?? null;
                $itemDate  = $item['billdate'] ?? $item['receivedat'] ?? null;
                $billKey   = $item['billkey'] ?? null;

                // Duplicate check: same location + same billdate + same billno (or billkey)
                $exists = BillingListModel::where('location_id', $locationKey)
                    ->where(function ($q) use ($dateFmt, $itemDate) {
                        if ($itemDate) {
                            $q->whereRaw('LEFT(billdate,8) = ?', [substr((string) $itemDate, 0, 8)]);
                        } else {
                            $q->whereRaw('LEFT(billdate,8) = ?', [$dateFmt]);
                        }
                    })
                    ->where(function ($q) use ($billNo, $receiptNo, $billKey) {
                        $q->when($billNo,    fn ($qq) => $qq->orWhere('billno',    $billNo))
                          ->when($receiptNo, fn ($qq) => $qq->orWhere('receiptno', $receiptNo))
                          ->when($billKey,   fn ($qq) => $qq->orWhere('billkey',   $billKey));
                    })
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                BillingListModel::create([
                    'location_id'        => $locationKey,
                    'location_name'      => $locName,
                    'type'               => $item['type'] ?? null,
                    'paymenttype'        => $item['paymenttype'] ?? null,
                    'amt'                => $item['amt'] ?? 0,
                    'billno'             => $billNo,
                    'billdate'           => $item['billdate'] ?? $item['receivedat'] ?? null,
                    'user_name'          => $item['user'] ?? $item['receivedby'] ?? null,
                    'userid'             => $item['userid'] ?? $item['receivedbyid'] ?? null,
                    'phid'               => $item['phid'] ?? null,
                    'extphid'            => $item['extphid'] ?? null,
                    'gender'             => $item['gender'] ?? null,
                    'age'                => $item['age'] ?? null,
                    'mobile'             => $item['mobile'] ?? null,
                    'ptsource'           => $item['ptsource'] ?? null,
                    'isdcode'            => $item['isdcode'] ?? null,
                    'dob'                => $item['dob'] ?? null,
                    'email'              => $item['email'] ?? null,
                    'patientname'        => $item['patientname'] ?? null,
                    'patientkey'         => $item['patientkey'] ?? null,
                    'consultant'         => $item['consultant'] ?? null,
                    'consultantkey'      => $item['consultantkey'] ?? null,
                    'referredbykey'      => $item['referredbykey'] ?? null,
                    'referredby'         => $item['referredby'] ?? null,
                    'provider'           => $item['provider'] ?? null,
                    'billkey'            => $billKey,
                    'billtype'           => $item['billtype'] ?? null,
                    'tax'                => $item['grandtax'] ?? $item['tax'] ?? 0,
                    'opno'               => $item['ipno'] ?? $item['opno'] ?? null,
                    'receiptno'          => $receiptNo,
                    'receivedat'         => $item['receivedat'] ?? null,
                    'grandtotal'         => $item['grandtotal'] ?? 0,
                    'granddiscountvalue' => $item['granddiscountvalue'] ?? 0,
                    'grandprodvalue'     => $item['grandprodvalue'] ?? 0,
                    'paymentinfo'        => isset($item['paymentinfo']) ? json_encode($item['paymentinfo']) : null,
                ]);

                $inserted++;
            } catch (\Throwable $e) {
                \Log::error('BillingStatsController::fetchAndInsert failed for one row: ' . $e->getMessage(), ['item' => $item]);
                $errors++;
            }
        }

        $dateLabel = Carbon::parse($request->date)->format('d M Y');
        $msg = "Fetched " . count($billingList) . " records from MocDoc for {$locName} on {$dateLabel}. "
             . "Inserted: {$inserted}, Skipped (duplicate): {$skipped}"
             . ($errors ? ", Errors: {$errors}" : '') . '.';

        return response()->json([
            'success'  => true,
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'errors'   => $errors,
            'total_api'=> count($billingList),
            'message'  => $msg,
        ]);
    }

    /**
     * Normalize MocDoc JSON: billing rows may live under different keys or nested under "data".
     *
     * @return list<array<string, mixed>>
     */
    private function extractMocdocBillingListFromDecoded(?array $decoded): array
    {
        if (! is_array($decoded)) {
            return [];
        }
        foreach (['billinglist', 'billingList', 'BillingList'] as $k) {
            if (! empty($decoded[$k]) && is_array($decoded[$k])) {
                return array_values($decoded[$k]);
            }
        }
        if (! empty($decoded['data']) && is_array($decoded['data'])) {
            foreach (['billinglist', 'billingList'] as $k) {
                if (! empty($decoded['data'][$k]) && is_array($decoded['data'][$k])) {
                    return array_values($decoded['data'][$k]);
                }
            }
        }

        return [];
    }

    /**
     * cURL POST to MocDoc API with retry & backoff.
     */
    private function callMocdocApi(string $url, string $date, string $locationId, int $maxRetries = 5): ?array
    {
        ini_set('max_execution_time', 360);
        $postFields = "date={$date}&entitylocation={$locationId}";
        // Keep Date aligned with other MocDoc callers (md-authorization may be bound to this value).
        $headers    = [
            'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
            'Date: Fri, 07 Mar 2025 10:07:52 GMT',
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: SRV=s1',
        ];

        $retry   = 0;
        $backoff = 1;

        do {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 120,
                CURLOPT_CONNECTTIMEOUT => 25,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $postFields,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_ENCODING       => '',
            ]);

            $response = curl_exec($curl);
            $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlErrno = curl_errno($curl);
            $curlError = curl_error($curl);
            $downloaded = curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD);
            curl_close($curl);

            if ($response === false) {
                \Log::error('BillingStats MocDoc: cURL transport error', [
                    'location_id' => $locationId,
                    'date_ymd'    => $date,
                    'post_fields' => $postFields,
                    'curl_errno'  => $curlErrno,
                    'curl_error'  => $curlError,
                    'attempt'     => $retry + 1,
                    'max_retries' => $maxRetries,
                ]);
                $retry++;
                sleep($backoff);
                $backoff = min($backoff * 2, 30);
                continue;
            }

            if ($httpCode === 429) {
                \Log::warning('BillingStats MocDoc: HTTP 429 rate limit', [
                    'location_id' => $locationId,
                    'date_ymd'    => $date,
                    'backoff_s'   => $backoff,
                ]);
                sleep($backoff);
                $backoff = min($backoff * 2, 30);
                $retry++;
                continue;
            }

            if ($httpCode !== 200) {
                $snippet = is_string($response) ? \Illuminate\Support\Str::limit(preg_replace('/\s+/u', ' ', $response), 600, '…') : '';
                \Log::warning('BillingStats MocDoc: non-200 HTTP response', [
                    'location_id'       => $locationId,
                    'date_ymd'          => $date,
                    'post_fields'       => $postFields,
                    'http_code'         => $httpCode,
                    'bytes_downloaded'  => $downloaded,
                    'response_snippet'  => $snippet,
                ]);
                if (in_array($httpCode, [502, 503, 504], true) && $retry < $maxRetries - 1) {
                    $retry++;
                    sleep($backoff);
                    $backoff = min($backoff * 2, 30);
                    continue;
                }

                return null;
            }

            $decoded = json_decode($response, true);
            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                \Log::warning('BillingStats MocDoc: JSON decode failed', [
                    'location_id'      => $locationId,
                    'date_ymd'         => $date,
                    'json_error'       => json_last_error_msg(),
                    'body_length'      => strlen((string) $response),
                    'body_snippet'     => \Illuminate\Support\Str::limit(preg_replace('/\s+/u', ' ', (string) $response), 600, '…'),
                ]);
                $retry++;
                sleep($backoff);
                $backoff = min($backoff * 2, 30);
                continue;
            }

            $rows = $this->extractMocdocBillingListFromDecoded(is_array($decoded) ? $decoded : []);
            if ($rows !== []) {
                \Log::info('BillingStats MocDoc: OK', [
                    'location_id'  => $locationId,
                    'date_ymd'     => $date,
                    'billing_rows' => count($rows),
                ]);
            } elseif (is_array($decoded)) {
                \Log::info('BillingStats MocDoc: HTTP 200 but no billinglist array (empty day or unexpected shape)', [
                    'location_id'   => $locationId,
                    'date_ymd'      => $date,
                    'post_fields'   => $postFields,
                    'decoded_keys'  => array_keys($decoded),
                    'body_length'   => strlen((string) $response),
                    'body_snippet'  => \Illuminate\Support\Str::limit(preg_replace('/\s+/u', ' ', (string) $response), 800, '…'),
                ]);
            }

            return is_array($decoded) ? $decoded : [];
        } while ($retry < $maxRetries);

        \Log::error('BillingStats MocDoc: exhausted retries', [
            'location_id' => $locationId,
            'date_ymd'    => $date,
            'post_fields' => $postFields,
        ]);

        return null;
    }
}