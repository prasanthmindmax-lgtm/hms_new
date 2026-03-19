<?php

namespace App\Imports;

use App\Models\Tblbill;
use App\Models\Tblvendor;
use App\Models\TblBillLines;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class billImport implements ToCollection, WithHeadingRow
{
    /** Number of bills skipped (already exist for same vendor + bill number) */
    public $existingCount = 0;

    /** Number of bills newly inserted */
    public $insertedCount = 0;

   public function collection(Collection $rows)
    {
        $grouped = $rows->groupBy('bill_no');
        $now = now();
        $user_id = auth()->user()->id;

        foreach ($grouped as $bill_no => $lines) {
            $first = $lines->first();
            // dd($first);
            // ✅ Add validation for required fields
            if (!isset($first['vendor_id'])) {
                Log::error("Missing vendor_id for Bill: " . $bill_no);
                continue; // Skip this quotation or handle accordingly
            }

            // ✅ Use null coalescing with proper fallbacks
            $vendor_id = trim($first['vendor_id'] ?? '');
            $bill_date = $this->formatDate(trim($first['bill_date'] ?? ''));
            $due_date = $this->formatDate(trim($first['due_date'] ?? ''));

            // ✅ Check if vendor_id is not empty
            if (empty($vendor_id)) {
                Log::error("Empty vendor_id for Bill: " . $bill_no);
                continue;
            }

            // ✅ Fetch vendor by vendor_id
            $vendor = Tblvendor::where('vendor_id', $vendor_id)->first();

            if (!$vendor) {
                Log::error("Vendor not found with vendor_id: " . $vendor_id);
                continue;
            }

            // ✅ Skip if bill already exists for this vendor + bill number
            if (Tblbill::where('vendor_id', $vendor->id)->where('bill_number', $bill_no)->exists()) {
                $this->existingCount++;
                continue;
            }

            // ✅ Generate Quotation ID
            $lastRecord = Tblbill::orderBy('id', 'DESC')->first();
                // dd(isset($lastRecord->bill_gen_number));
                if ($lastRecord && isset($lastRecord->bill_gen_number)) {
                    $lastNumber = (int) str_replace('BILL-', '', $lastRecord->bill_gen_number);
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

            $serial = 'BILL-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            // --- Initialize values ---
            $sub_total = 0;
            $gst_total = 0;
            $tds_total = 0;
            $discount_amount = 0;
            $adjustment_value = (float) ($first['adjustment'] ?? 0);

            // --- Pre-calculate Subtotal and GST ---
            foreach ($lines as $row) {
                $qty = (float) ($row['quantity'] ?? 0);
                $rate = (float) ($row['rate'] ?? 0);
                $amount = $qty * $rate;
                $sub_total += $amount;

                // --- GST calculation ---
                if (!empty($row['gst'] ?? '')) {
                    $gstName = trim($row['gst']);
                    $gstRecord = DB::table('gst_tax_tbl')
                        ->where(DB::raw("CONCAT(tax_name, ' - ', tax_rate)"), $gstName)
                        ->first();

                    if ($gstRecord) {
                        $gst_rate = (float) $gstRecord->tax_rate;
                        $gst_type = $gstRecord->tax_type ?? 'interstate';

                        if (strtolower($gst_type) === 'intra') {
                            $cgst = ($amount * ($gst_rate / 2)) / 100;
                            $sgst = ($amount * ($gst_rate / 2)) / 100;
                            $gst_total += ($cgst + $sgst);
                        } else {
                            $igst = ($amount * $gst_rate) / 100;
                            $gst_total += $igst;
                        }
                    }
                }
            }

            // ✅ TDS calculation
            $tds_rate = 0;
            $tdsName = '';
            $tds_id = null;
            if (!empty($first['tds'] ?? '')) {
                $tdsName = trim($first['tds']);
                $tdsRecord = DB::table('tds_tax_tbl')
                    ->where(DB::raw("CONCAT(tax_name, ' - ', tax_rate)"), $tdsName)
                    ->first();

                if ($tdsRecord) {
                    $tds_rate = (float) $tdsRecord->tax_rate;
                    $tds_id = $tdsRecord->id;
                    $tds_total = ($sub_total * $tds_rate) / 100;
                }
                if (!empty($tdsName) && str_contains($tdsName, '-')) {
                    $tdsName = preg_replace('/\s*-\s*/', ' [', $tdsName);
                    $tdsName = $tdsName . '%]';
                }
            }

            $zone_id = 0;
            $zoneName = '';
            if (!empty($first['zone'] ?? '')) {
                $zoneName = trim($first['zone']);
                $zoneRecord = DB::table('tblzones')
                    ->where('name', $zoneName)
                    ->first();
                $zone_id = $zoneRecord ? $zoneRecord->id : 0;
            }

            $branch_id = 0;
            $branchName = '';
            if (!empty($first['branch'] ?? '')) {
                $branchName = trim($first['branch']);
                $branchRecord = DB::table('tbl_locations')
                    ->where('name', $branchName)
                    ->first();
                $branch_id = $branchRecord ? $branchRecord->id : 0;
            }

            $company_id = 0;
            $companyName = '';
            if (!empty($first['company'] ?? '')) {
                $companyName = trim($first['company']);
                $companyRecord = DB::table('company_tbl')
                    ->where('company_name', $companyName)
                    ->first();
                $company_id = $companyRecord ? $companyRecord->id : 0;
            }

            // ✅ FIX: Remove duplicate zone_id assignment
            // ✅ Discount calculation
         // ---------- DISCOUNT ----------
            $discountType = strtolower($first['discount_type'] ?? 'percent');
            $discountValue = (float) ($first['discount'] ?? 0);

            if ($discountType === 'percent') {
                $discount_amount = ($sub_total * $discountValue) / 100;
            } else {
                $discount_amount = $discountValue;
            }


            // ---------- ESI ----------
            $esiType = strtolower($first['esi_type'] ?? 'percent');
            $esiValue = (float) ($first['esi_value'] ?? 0);

            if ($esiType === 'percent') {
                $esi_amount = ($sub_total * $esiValue) / 100;
            } else {
                $esi_amount = $esiValue;
            }


            // ---------- PF ----------
            $pfType = strtolower($first['pf_type'] ?? 'percent');
            $pfValue = (float) ($first['pf_value'] ?? 0);

            if ($pfType === 'percent') {
                $pf_amount = ($sub_total * $pfValue) / 100;
            } else {
                $pf_amount = $pfValue;
            }


            // ---------- OTHER ----------
            $otherType = strtolower($first['other_type'] ?? 'percent');
            $otherValue = (float) ($first['other_value'] ?? 0);

            if ($otherType === 'percent') {
                $other_amount = ($sub_total * $otherValue) / 100;
            } else {
                $other_amount = $otherValue;
            }


            // ---------- FINAL TOTAL ----------
            $grand_total =
                    $sub_total
                    + $gst_total
                    - $tds_total
                    - $discount_amount
                    - $esi_amount
                    - $pf_amount
                    - $other_amount
                    + $adjustment_value;

            // ✅ Save bill header first
            $bill = Tblbill::create([
                'user_id' => $user_id,
                'vendor_id' => $vendor->id,
                'vendor_name' => $first['vendor_name'] ?? '',
                'zone_id' => $zone_id, // ✅ Fixed: Only assign once
                'zone_name' => $zoneName, // ✅ Corrected field name
                'branch_id' => $branch_id,
                'branch_name' => $branchName,
                'company_id' => $company_id,
                'company_name' => $companyName,
                'bill_number' => $bill_no,
                'bill_gen_number' => $serial,
                'delivery_address' => $first['delivery_address'] ?? '',
                'order_number' => $first['order_number'] ?? '',
                'bill_date' => $bill_date,
                'due_date' => $due_date,
                'payment_terms' => $first['payment_terms'] ?? '',
                'subject' => $first['subject'] ?? '',
                'discount_percent' => $discountValue,
                'discount_type' => $discountType,
                'discount_amount' => $discount_amount,
                'adjustment_amount' => $adjustment_value,
                'tax_type' => 'TDS',
                'tax_name' => $tdsName,
                'tds_tax_id' => $tds_id,
                'tax_rate' => $tds_rate,
                'tax_amount' => $tds_total,
                'sub_total_amount' => $sub_total,
                'grand_total_amount' => $grand_total,
                'balance_amount' => $grand_total,
                'bill_status' =>'Due to Pay',
                'status' => 'draft',
                'esi_type' => $esiType,
                'esi_value' => $esiValue,
                'pf_type' => $pfType,
                'pf_value' => $pfValue,
                'other_type' => $otherType,
                'other_value' => $otherValue,
                'other_reason' => $first['PT/Other Reason'] ?? '',
                'esi_amount' => $esi_amount,
                'pf_amount' => $pf_amount,
                'other_amount' => $other_amount,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->insertedCount++;

            // ✅ Then save bill lines with proper null checks
            foreach ($lines as $row) {
                $qty = (float) ($row['quantity'] ?? 0);
                $rate = (float) ($row['rate'] ?? 0);
                $amount = $qty * $rate;

                $gst_rate = 0;
                $cgst_amount = 0;
                $sgst_amount = 0;
                $gst_amount = 0;
                $gst_type = null;
                $gstName = '';
                $gst_id = '';

                if (!empty($row['gst'] ?? '')) {
                    $gstName = trim($row['gst']);
                    $gstRecord = DB::table('gst_tax_tbl')
                        ->where(DB::raw("CONCAT(tax_name, ' - ', tax_rate)"), $gstName)
                        ->first();

                    if ($gstRecord) {
                        $gst_id = $gstRecord->id;
                        $gst_rate = (float) $gstRecord->tax_rate;
                        $gst_type = $gstRecord->tax_type ?? 'interstate';

                        if (strtolower($gst_type) === 'gst') {
                            $cgst_amount = ($amount * ($gst_rate / 2)) / 100;
                            $sgst_amount = ($amount * ($gst_rate / 2)) / 100;
                            $gst_amount = $cgst_amount + $sgst_amount;
                        } else {
                            $gst_amount = ($amount * $gst_rate) / 100;
                        }
                    }

                    if (!empty($gstName) && str_contains($gstName, '-')) {
                        $gstName = preg_replace('/\s*-\s*/', ' [', $gstName);
                        $gstName = $gstName . '%]';
                    }
                }

                $account_id = 0;
                $accountName = '';
                if (!empty($first['account'] ?? '')) {
                    $accountName = trim($first['account']);
                    $accountRecord = DB::table('account_tbl')
                        ->where('name', $accountName)
                        ->first();
                    $account_id = $accountRecord ? $accountRecord->id : 0;
                }

                TblBillLines::create([
                    'bill_id' => $bill->id,
                    'item_details' => $row['item_details'] ?? '',
                    'account' => $accountName,
                    'account_id' => $account_id,
                    'quantity' => $qty,
                    'rate' => $rate,
                    'customer' => $row['customer'] ?? '',
                    'amount' => $amount,
                    'gst_tax_id' => $gst_id,
                    'gst_rate' => $gst_rate,
                    'gst_name' => $gstName,
                    'gst_amount' => $gst_amount,
                    'cgst_amount' => $cgst_amount,
                    'sgst_amount' => $sgst_amount,
                    'gst_type' => $gst_type,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function formatDate($value)
    {
        if (empty($value)) return null;

        // Handle Excel numeric dates
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('d/m/Y');
        }

        // Handle string dates
        try {
            // First try to read as d/m/Y
            return Carbon::createFromFormat('d/m/Y', $value)->format('d/m/Y');
        } catch (\Exception $e) {
            // Try additional formats if needed (optional)
            try {
                return Carbon::parse($value)->format('d/m/Y');
            } catch (\Exception $e) {
                Log::error("Invalid date format: " . $value);
                return null;
            }
        }
    }



}
