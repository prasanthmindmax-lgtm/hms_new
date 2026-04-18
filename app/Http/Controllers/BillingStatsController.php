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
}