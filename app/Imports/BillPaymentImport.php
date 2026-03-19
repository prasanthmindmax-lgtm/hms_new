<?php

namespace App\Imports;

use App\Models\Tblbillpay;
use App\Models\Tblbill;
use App\Models\TblBillPayLines;
use App\Models\Tblvendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class BillPaymentImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $user_id = Auth::id();
        $now = now();

        foreach ($rows as $key => $row) {

            if ($key == 0) continue; // skip headings
            // dd($row);
            $billNumber       = trim($row[0] ?? '');
            $vendorId         = trim($row[1] ?? '');
            $vendorName       = trim($row[2] ?? '');
            $paymentDate      = trim($row[3] ?? '');
            $paidMode         = trim($row[4] ?? '');
            $paymentMadeDate  = trim($row[5] ?? '');
            $amountPaid       = trim($row[6] ?? 0);
            $zoneName         = trim($row[7] ?? '');
            $branchName       = trim($row[8] ?? '');
            $companyName      = trim($row[9] ?? '');
            $payment      = 'NEFT';
            // dd($paidThrough);

            // ------------- GET IDs using NAMES (LIKE YOU REQUIRED) -------------
            $zone_id = 0;
            if ($zoneName !== '') {
                $zoneRecord = DB::table('tblzones')->where('name', $zoneName)->first();
                $zone_id = $zoneRecord ? $zoneRecord->id : 0;
            }

            $branch_id = 0;
            if ($branchName !== '') {
                $branchRecord = DB::table('tbl_locations')->where('name', $branchName)->first();
                $branch_id = $branchRecord ? $branchRecord->id : 0;
            }

            $company_id = 0;
            if ($companyName !== '') {
                $companyRecord = DB::table('company_tbl')->where('company_name', $companyName)->first();
                $company_id = $companyRecord ? $companyRecord->id : 0;
            }
            $vendor = Tblvendor::where('vendor_id', $vendorId)->first();
            // dd($vendor);
            // ------------------------------------------------------------------

            $bill = Tblbill::where('bill_gen_number', $billNumber)->first();
            // dd($bill);
            if (!$bill) continue;
            // Calculate new values
            $balance = $bill->grand_total_amount - $amountPaid;
            $partial = $bill->partially_payment + $amountPaid;
            // dd($partial);
            // Update bill status
            if ($balance == 0) {
                $bill->update([
                    'partially_payment' => $partial,
                    'balance_amount' => $balance,
                    'bill_made_status' => 1,
                    'bill_status' => 'Paid',
                ]);
            } else {
                $bill->update([
                    'partially_payment' => $partial,
                    'balance_amount' => $balance,
                    'bill_status' => 'Partially Payed',
                ]);
            }
            $lastRecord = Tblbillpay::orderBy('id', 'DESC')->first();
            if ($lastRecord && isset($lastRecord->payment_gen_order)) {
                $lastNumber = (int) str_replace('PAYMENT-', '', $lastRecord->payment_gen_order);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            $serial = 'PAYMENT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            // dd($serial);

            // Insert billpay record
            $billPay = Tblbillpay::create([
                'user_id'       => $user_id,
                'vendor_id'     => $vendor->id,
                'vendor_name'   => $vendor->display_name,
                'payment_gen_order'  => $serial,
                'payment'       => $payment,
                'zone_id'       => $zone_id,
                'zone_name'     => $zoneName,
                'branch_id'     => $branch_id,
                'branch_name'   => $branchName,
                'company_id'    => $company_id,
                'company_name'  => $companyName,
                'payment_date'  => $paymentDate,
                'payment_mode'  => $paidMode,
                'amount_paid'   => $bill->grand_total_amount,
                'amount_used'   => $amountPaid,
                'save_status'   => 'Imported',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);

            // Save bill payment lines
            TblBillPayLines::create([
                'bill_pay_id' => $billPay->id,
                'bill_id' => $bill->id,
                'bill_date' => $bill->bill_date,
                'due_date' => $bill->due_date,
                'bill_number' => $bill->bill_number,
                'grand_total_amount' => $bill->grand_total_amount,
                'balance_amount' => $balance,
                'payment_date' => $paymentMadeDate,
                'amount' => $amountPaid,
                'created_at' => $now,
            ]);
        }
    }
}
