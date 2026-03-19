<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class IncomeImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Skip first 2 header rows
        $rows = $rows->skip(2);

        foreach ($rows as $row) {

            if (!$row[2] || !$row[3]) continue;   // Branch + Date required
            // Convert row to array safely
            $row = $row->toArray();
            // -----------------------------------------
            // 1️⃣ Skip FULLY EMPTY ROWS
            // -----------------------------------------
            if (collect($row)->filter(fn($v) => trim((string)$v) !== '')->isEmpty()) {
                continue;
            }

            $zone   = trim($row[1]);
            $branch = trim($row[2]);
            $excelDate = $row[3];

            // -------- FORMAT DATE OR DATE RANGE ----------
            $date = $this->formatExcelDateRange($excelDate);

            DB::table('income_reconciliation_table')->updateOrInsert(
                [
                    'location_name' => $branch,
                    'date_range'    => $date
                ],
                [
                    'zone_name' => $zone,

                    // CASH
                    'cash_moc_amt'           => $row[4]  ?? 0,
                    'cash_date_filter'       => $this->formatExcelDateRange($row[5] ?? null),
                    'cash_date_amt_filter'   => $row[6]  ?? 0,
                    'cash_radiant'           => $row[7]  ?? null,
                    'cash_radiant_diff'           => $row[8]  ?? null,
                    'cash_bank'              => $row[9]  ?? 0,
                    'cash_bank_diff'              => $row[10]  ?? 0,
                    'cash_radiant_remark'    => $row[11] ?? null,

                    // CARD
                    'card_moc_amt'           => $row[12] ?? 0,
                    'card_date_filter'       => $this->formatExcelDateRange($row[13] ?? null),
                    'card_date_amt_filter'   => $row[14] ?? 0,
                    'card_radiant'           => $row[15] ?? null,
                    'card_radiant_diff'           => $row[16] ?? null,
                    'card_radiant_remark'    => $row[17] ?? null,

                    // UPI
                    'upi_moc_amt'            => $row[18] ?? 0,
                    'upi_date_filter'        => $this->formatExcelDateRange($row[19] ?? null),
                    'upi_date_amt_filter'    => $row[20] ?? 0,
                    'upi_radiant'            => $row[21] ?? null,
                    'upi_radiant_diff'            => $row[22] ?? null,
                    'upi_radiant_remark'     => $row[23] ?? null,

                    // BANK STATEMENT
                    'bank_stmt_charge'       => $row[24] ?? 0,
                    'bank_stmt_amount'       => $row[25] ?? 0,
                    'bank_stmt_diff'         => $row[26] ?? 0,

                    // NEFT
                    'neft_moc_amt'           => $row[27] ?? 0,
                    'neft_date_filter'       => $this->formatExcelDateRange($row[28] ?? null),
                    'neft_date_amt_filter'   => $row[29] ?? 0,
                    'neft_bank'              => $row[30] ?? 0,
                    'neft_bank_diff'              => $row[31] ?? 0,
                    'neft_radiant_remark'    => $row[32] ?? null,

                    // TOTAL
                    'total_moc_amt' =>
                        ($row[4] ?? 0) +
                        ($row[12] ?? 0) +
                        ($row[18] ?? 0) +
                        ($row[27] ?? 0),

                    'status'     => 1,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }
    }

    /**
     * Convert Excel serial / text date / date range
     * to d/m/Y or d/m/Y - d/m/Y
     */
    private function formatExcelDateRange($value)
    {
        if (!$value) return null;

        $value = trim($value);

        // cases like "-" or empty
        if ($value === '-' || $value === '') {
            return null;
        }

        // -------- RANGE CASE --------
        if (str_contains($value, ' - ')) {

            [$start, $end] = explode(' - ', $value);

            $start = trim($start);
            $end   = trim($end);

            $startFormatted = $this->formatSingleDate($start);
            $endFormatted   = $this->formatSingleDate($end);

            // both missing
            if (!$startFormatted && !$endFormatted) {
                return null;
            }

            // only end present
            if (!$startFormatted && $endFormatted) {
                return $endFormatted;
            }

            // only start present
            if ($startFormatted && !$endFormatted) {
                return $startFormatted;
            }

            // both present
            return $startFormatted . ' - ' . $endFormatted;
        }

        // -------- SINGLE DATE --------
        return $this->formatSingleDate($value);
    }


    /**
     * Convert single date or return null if not valid
     */
    private function formatSingleDate($date)
    {
        if (!$date) return null;

        $date = trim($date);

        if ($date === '-' || $date === '') return null;

        try {
            if (is_numeric($date)) {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)
                )->format('d/m/Y');
            }

            return Carbon::parse($date)->format('d/m/Y');

        } catch (\Exception $e) {
            return null;
        }
    }

}
