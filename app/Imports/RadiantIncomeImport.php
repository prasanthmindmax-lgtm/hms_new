<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class RadiantIncomeImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Skip header rows (change count if needed)
        $rows = $rows->skip(2);
        foreach ($rows as $row) {
            $row = $row->toArray();
            // dd($row);
            // Skip fully empty rows
            if (collect($row)->filter(fn ($v) => trim((string)$v) !== '')->isEmpty()) {
                continue;
            }

            /* ---------------------------------------------
               REQUIRED FIELDS
            --------------------------------------------- */
            $locationName = trim($row[2] ?? '');
            $dateRange    = $this->formatExcelDateRange($row[3] ?? null);

            if (!$locationName || !$dateRange) {
                continue;
            }
            /* ---------------------------------------------
               RESOLVE ZONE (same as storeRadiant)
            --------------------------------------------- */
            $zoneName = trim($row[1] ?? '');

            if (!$zoneName) {
                $location = DB::table('tbl_locations')
                    ->where('name', $locationName)
                    ->first();

                if (!$location) continue;

                $zone = DB::table('tblzones')
                    ->where('id', $location->zone_id)
                    ->first();

                if (!$zone) continue;

                $zoneName = $zone->name;
            }

            /* ---------------------------------------------
               DATA MAPPING (MATCH storeRadiant)
            --------------------------------------------- */
            $data = [
                'zone_name'          => $zoneName,
                'location_name'      => $locationName,
                'date_range'         => $dateRange,

                // MOC
                'moc_cash_amt'       => $row[4]  ?? 0,
                'moc_card_amt'       => $row[5]  ?? 0,
                'moc_upi_amt'        => $row[6]  ?? 0,
                'moc_total_upi_card' => $row[7]  ?? 0,
                'moc_neft_amt'       => $row[8]  ?? 0,
                'moc_other_amt'      => $row[9]  ?? 0,
                'moc_overall_total'  => $row[10]  ?? 0,

                // COLLECTION
                'date_collection'    => $this->formatExcelDateRange($row[11] ?? null),
                'collection_amount' => $row[12] ?? 0,

                // DEPOSIT
                'date_deposited'     => $this->formatExcelDateRange($row[13] ?? null),
                'deposite_amount'    => $row[14] ?? 0,
                'cash_utr_number'    => $row[15] ?? '-',

                // MESPOS
                'mespos_card'        => $row[16] ?? 0,
                'mespos_upi'         => $row[17] ?? 0,
                'date_settlement'    => $this->formatExcelDateRange($row[18] ?? null),

                // BANK
                'bank_chargers'      => $row[19] ?? 0,
                'bank_upi_card'      => $row[20] ?? 0,
                'bank_upi_card_utr'      => $row[21] ?? '-',
                'bank_neft'          => $row[22] ?? 0,
                'bank_neft_utr'          => $row[23] ?? '-',
                'bank_others'        => $row[24] ?? 0,
                'bank_other_utr'        => $row[25] ?? '-',

                // DIFFERENCE
                'radiant_diff'       => $row[26] ?? 0,
                'cash_diff'          => $row[27] ?? 0,
                'card_upi_diff'      => $row[28] ?? 0,
                'neft_others_diff'   => $row[29] ?? 0,
                // REMARK
                'remark'             => $row[30] ?? null,
                'updated_at'         => now(),
            ];
            /* ---------------------------------------------
               INSERT / UPDATE (REFERENCE KEY)
            --------------------------------------------- */
            DB::table('income_reconciliation_table')->updateOrInsert(
                [
                    'location_name' => $locationName,
                    'date_range'    => $dateRange
                ],
                array_merge($data, ['created_at' => now()])
            );
        }
    }

    /* ---------------------------------------------
       DATE FORMATTERS
    --------------------------------------------- */
    private function formatExcelDateRange($value)
    {
        if (!$value) return null;

        $value = trim((string)$value);

        if ($value === '-' || $value === '') return null;

        if (str_contains($value, ' - ')) {
            [$start, $end] = explode(' - ', $value);

            $start = $this->formatSingleDate(trim($start));
            $end   = $this->formatSingleDate(trim($end));

            if (!$start && !$end) return null;
            if ($start && !$end) return $start;
            if (!$start && $end) return $end;

            return $start . ' - ' . $end;
        }

        return $this->formatSingleDate($value);
    }

    private function formatSingleDate($date)
    {
        if (!$date || $date === '-') return null;

        try {
            if (is_numeric($date)) {
                return Carbon::instance(
                    ExcelDate::excelToDateTimeObject($date)
                )->format('d/m/Y');
            }

            return Carbon::parse($date)->format('d/m/Y');
        } catch (\Exception $e) {
            return null;
        }
    }
}
