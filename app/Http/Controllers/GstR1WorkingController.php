<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GstR1Working;
use App\Models\TblZonesModel;
use App\Models\TblLocationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GstR1WorkingController extends Controller
{
    /* =====================================================================
       INDEX — main page (returns view only)
       ===================================================================== */
    public function index()
    {
        $admin       = auth()->user();
        $allZones    = TblZonesModel::orderBy('name')->get(['id', 'name']);
        $allBranches = TblLocationModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'zone_id']);
        $dbStates    = GstR1Working::select('state')->whereNotNull('state')->where('state', '!=', '')->distinct()->orderBy('state')->pluck('state');

        return view('vendor.gst_r1_working', compact('admin', 'allZones', 'allBranches', 'dbStates'));
    }

    /* =====================================================================
       AJAX LIST
       ===================================================================== */
    public function list(Request $request)
    {
        $q = GstR1Working::query();

        if ($request->filled('search'))  {
            $s = $request->search;
            $q->where(function($w) use ($s) {
                $w->where('branch','like',"%$s%")->orWhere('zone','like',"%$s%")->orWhere('state','like',"%$s%");
            });
        }
        if ($request->filled('month'))   { $q->where('month', $request->month); }
        if ($request->filled('source'))  { $q->where('source', $request->source); }
        // Multi-value filters (comma-separated)
        if ($request->filled('states')) {
            $q->whereIn('state', array_filter(explode(',', $request->states)));
        }
        if ($request->filled('zones')) {
            $q->whereIn('zone', array_filter(explode(',', $request->zones)));
        }
        if ($request->filled('branches')) {
            $q->whereIn('branch', array_filter(explode(',', $request->branches)));
        }

        $sort = in_array($request->sort, ['branch','zone','month','total_turnover','collection','difference','created_at'])
            ? $request->sort : 'created_at';
        $dir  = $request->dir === 'asc' ? 'asc' : 'desc';

        $total   = $q->count();
        $perPage = (int)($request->per_page ?? 25);
        $page    = (int)($request->page    ?? 1);
        $items   = $q->orderBy($sort, $dir)->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Summary totals (same filters as main query)
        $totals = GstR1Working::query()
            ->when($request->filled('month'),    fn($w) => $w->where('month',  $request->month))
            ->when($request->filled('source'),   fn($w) => $w->where('source', $request->source))
            ->when($request->filled('states'),   fn($w) => $w->whereIn('state',  array_filter(explode(',', $request->states))))
            ->when($request->filled('zones'),    fn($w) => $w->whereIn('zone',   array_filter(explode(',', $request->zones))))
            ->when($request->filled('branches'), fn($w) => $w->whereIn('branch', array_filter(explode(',', $request->branches))))
            ->when($request->filled('search'),   fn($w) => $w->where(function($q) use ($request) {
                $s = $request->search;
                $q->where('branch','like',"%$s%")->orWhere('zone','like',"%$s%")->orWhere('state','like',"%$s%");
            }))
            ->selectRaw('
                SUM(total_pharmacy) as sum_pharmacy,
                SUM(total_gst)      as sum_gst,
                SUM(exempt_sales)   as sum_exempt,
                SUM(total_turnover) as sum_turnover,
                SUM(collection)     as sum_collection,
                SUM(difference)     as sum_difference
            ')->first();

        return response()->json([
            'data'       => $items,
            'total'      => $total,
            'per_page'   => $perPage,
            'page'       => $page,
            'last_page'  => (int)ceil($total / $perPage),
            'totals'     => $totals,
        ]);
    }

    /* =====================================================================
       STORE — manual create
       ===================================================================== */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'zone'   => 'nullable|string|max:100',
            'branch' => 'required|string|max:150',
            'month'  => 'required|string|max:50',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        // Check duplicate
        $exists = GstR1Working::where('branch', $request->branch)->where('month', $request->month)->first();
        if ($exists) return response()->json(['success' => false, 'message' => 'Record already exists for this Branch + Month combination.'], 409);

        $record = GstR1Working::create(array_merge($this->numericFields($request), ['source' => 'manual']));
        return response()->json(['success' => true, 'data' => $record]);
    }

    /* =====================================================================
       UPDATE
       ===================================================================== */
    public function update(Request $request, $id)
    {
        $record = GstR1Working::findOrFail($id);

        $v = Validator::make($request->all(), [
            'zone'   => 'nullable|string|max:100',
            'branch' => 'required|string|max:150',
            'month'  => 'required|string|max:50',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        // Check duplicate on another row
        $dup = GstR1Working::where('branch', $request->branch)->where('month', $request->month)->where('id', '!=', $id)->first();
        if ($dup) return response()->json(['success' => false, 'message' => 'Another record already exists for this Branch + Month.'], 409);

        $record->update($this->numericFields($request));
        return response()->json(['success' => true, 'data' => $record->fresh()]);
    }

    /* =====================================================================
       DESTROY
       ===================================================================== */
    public function destroy($id)
    {
        GstR1Working::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /* =====================================================================
       IMPORT — Excel upload
       ===================================================================== */
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);

        $file        = $request->file('file');
        $overwrite   = $request->boolean('overwrite', false);
        $spreadsheet = IOFactory::load($file->getRealPath());

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        // Pre-load location→zone lookup map (branch name → zone name) for auto-fill
        $locationZoneMap = TblLocationModel::where('status', 1)
            ->with('zone')
            ->get()
            ->mapWithKeys(fn($loc) => [
                strtolower(trim($loc->name)) => $loc->zone?->name
            ]);

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $month = trim($sheet->getTitle()); // e.g. "April 2026"
            $rows  = $sheet->toArray(null, true, true, false);

            // Data rows start at index 2 (0-based); first 2 rows are headers
            $state = null; // geographical state from grouping rows (Tamil Nadu, etc.)
            foreach ($rows as $ri => $row) {
                if ($ri < 2) continue; // skip header rows

                $branch = trim((string)($row[0] ?? ''));
                if ($branch === '' || $branch === 'Branch') continue;

                // Detect state/zone summary rows (Tamil Nadu, Kerala, Karnataka, etc.)
                $summaryPatterns = ['Tamil Nadu','Andhra Pradesh','Kerala','Karnataka',
                                    'Telangana','Pondicherry','Puducherry','ISWARY','TURN OVER'];
                $isSummary = false;
                foreach ($summaryPatterns as $p) {
                    if (stripos($branch, $p) !== false) { $isSummary = true; break; }
                }
                if ($isSummary) {
                    $state = $branch; // store state name; next rows belong to this state
                    continue;
                }
                // Empty branch after header — reset attempt
                if (empty($branch)) continue;

                // Auto-lookup zone from tbl_locations when no grouping rows in Excel
                $autoZone = $locationZoneMap[strtolower($branch)] ?? null;

                $data = [
                    'zone'          => $autoZone, // auto-resolved from tbl_locations → tblzones
                    'state'         => $state,    // from Excel state grouping rows (may be null)
                    'branch'        => $branch,
                    'month'         => $month,
                    'fin_year'      => 2026,
                    'gst0_qty'      => $this->n($row[1]  ?? 0),
                    'gst0_taxable'  => $this->n($row[2]  ?? 0),
                    'gst5_qty'      => $this->n($row[3]  ?? 0),
                    'gst5_taxable'  => $this->n($row[4]  ?? 0),
                    'gst5_cgst'     => $this->n($row[5]  ?? 0),
                    'gst5_sgst'     => $this->n($row[6]  ?? 0),
                    'gst12_qty'     => $this->n($row[7]  ?? 0),
                    'gst12_taxable' => $this->n($row[8]  ?? 0),
                    'gst12_cgst'    => $this->n($row[9]  ?? 0),
                    'gst12_sgst'    => $this->n($row[10] ?? 0),
                    'gst18_qty'     => $this->n($row[11] ?? 0),
                    'gst18_taxable' => $this->n($row[12] ?? 0),
                    'gst18_cgst'    => $this->n($row[13] ?? 0),
                    'gst18_sgst'    => $this->n($row[14] ?? 0),
                    'total_pharmacy'=> $this->n($row[15] ?? 0),
                    'total_gst'     => $this->n($row[16] ?? 0),
                    'exempt_sales'  => $this->n($row[17] ?? 0),
                    'total_turnover'=> $this->n($row[18] ?? 0),
                    'collection'    => $this->n($row[19] ?? 0),
                    'difference'    => $this->n($row[20] ?? 0),
                    'source'        => 'import',
                ];

                try {
                    $existing = GstR1Working::where('branch', $branch)->where('month', $month)->first();
                    if ($existing) {
                        if ($overwrite) { $existing->update($data); $updated++; }
                        else { $skipped++; }
                    } else {
                        GstR1Working::create($data);
                        $inserted++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$ri} ({$branch}): " . $e->getMessage();
                }
            }
        }

        return response()->json([
            'success'  => true,
            'inserted' => $inserted,
            'updated'  => $updated,
            'skipped'  => $skipped,
            'errors'   => $errors,
            'message'  => "Import complete: {$inserted} inserted, {$updated} updated, {$skipped} skipped.",
        ]);
    }

    /* =====================================================================
       EXPORT — XLSX
       ===================================================================== */
    public function exportXlsx(Request $request)
    {
        $rows = $this->getExportRows($request);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GST R1 Workings');

        // Header styling
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F7B6C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ];

        $headers = [
            'State','Zone','Branch','Month','GST0 Qty','GST0 Taxable',
            'GST5 Qty','GST5 Taxable','GST5 CGST','GST5 SGST',
            'GST12 Qty','GST12 Taxable','GST12 CGST','GST12 SGST',
            'GST18 Qty','GST18 Taxable','GST18 CGST','GST18 SGST',
            'Total Pharmacy','Total GST','Exempt Sales',
            'Total Turnover','Collection','Difference','Source',
        ];

        foreach ($headers as $ci => $h) {
            $cell = $sheet->getCellByColumnAndRow($ci + 1, 1);
            $cell->setValue($h);
        }
        $sheet->getStyle('A1:' . $sheet->getCellByColumnAndRow(count($headers), 1)->getCoordinate())
              ->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $ri = 2;
        foreach ($rows as $row) {
            $sheet->fromArray([
                $row->state, $row->zone, $row->branch, $row->month,
                $row->gst0_qty,   $row->gst0_taxable,
                $row->gst5_qty,   $row->gst5_taxable,  $row->gst5_cgst,  $row->gst5_sgst,
                $row->gst12_qty,  $row->gst12_taxable, $row->gst12_cgst, $row->gst12_sgst,
                $row->gst18_qty,  $row->gst18_taxable, $row->gst18_cgst, $row->gst18_sgst,
                $row->total_pharmacy, $row->total_gst,  $row->exempt_sales,
                $row->total_turnover, $row->collection, $row->difference,
                $row->source,
            ], null, 'A' . $ri);
            $ri++;
        }

        // Auto-width
        foreach (range('A', 'X') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'GST_R1_Workings_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /* =====================================================================
       EXPORT — CSV
       ===================================================================== */
    public function exportCsv(Request $request): StreamedResponse
    {
        $rows     = $this->getExportRows($request);
        $filename = 'GST_R1_Workings_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = [
            'state','zone','branch','month',
            'gst0_qty','gst0_taxable',
            'gst5_qty','gst5_taxable','gst5_cgst','gst5_sgst',
            'gst12_qty','gst12_taxable','gst12_cgst','gst12_sgst',
            'gst18_qty','gst18_taxable','gst18_cgst','gst18_sgst',
            'total_pharmacy','total_gst','exempt_sales',
            'total_turnover','collection','difference','source',
        ];

        return response()->stream(function () use ($rows, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_map('strtoupper', $columns));
            foreach ($rows as $row) {
                fputcsv($handle, array_map(fn($c) => $row->{$c}, $columns));
            }
            fclose($handle);
        }, 200, $headers);
    }

    /* =====================================================================
       SHOW — single record (for edit modal)
       ===================================================================== */
    public function show($id)
    {
        return response()->json(GstR1Working::findOrFail($id));
    }

    /* =====================================================================
       HELPERS
       ===================================================================== */
    private function n($val): float
    {
        return (float)str_replace([',', ' ', '₹'], '', (string)$val);
    }

    private function numericFields(Request $request): array
    {
        $fields = [
            'zone', 'state', 'branch', 'month', 'fin_year',
            'gst0_qty', 'gst0_taxable',
            'gst5_qty', 'gst5_taxable', 'gst5_cgst', 'gst5_sgst',
            'gst12_qty', 'gst12_taxable', 'gst12_cgst', 'gst12_sgst',
            'gst18_qty', 'gst18_taxable', 'gst18_cgst', 'gst18_sgst',
            'total_pharmacy', 'total_gst', 'exempt_sales',
            'total_turnover', 'collection', 'difference',
        ];
        $data = [];
        foreach ($fields as $f) {
            if ($request->has($f)) {
                $data[$f] = in_array($f, ['zone','state','branch','month']) ? $request->input($f) : $this->n($request->input($f, 0));
            }
        }
        return $data;
    }

    private function getExportRows(Request $request)
    {
        return GstR1Working::query()
            ->when($request->filled('month'),    fn($q) => $q->where('month',   $request->month))
            ->when($request->filled('source'),   fn($q) => $q->where('source',  $request->source))
            ->when($request->filled('states'),   fn($q) => $q->whereIn('state',  array_filter(explode(',', $request->states))))
            ->when($request->filled('zones'),    fn($q) => $q->whereIn('zone',   array_filter(explode(',', $request->zones))))
            ->when($request->filled('branches'), fn($q) => $q->whereIn('branch', array_filter(explode(',', $request->branches))))
            ->orderBy('state')->orderBy('zone')->orderBy('branch')->orderBy('month')
            ->get();
    }
}