<?php

namespace App\Imports;

use App\Models\Tblvendor;
use App\Models\TblBilling;
use App\Models\TblShipping;
use App\Models\Tblbankdetails;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VendorImport implements ToModel, WithHeadingRow
{
    private $currentNumber;

    public function __construct()
    {
        // Get the last vendor number once for performance
        $lastVendor = Tblvendor::orderBy('id', 'desc')->first();
        $this->currentNumber = $lastVendor && $lastVendor->vendor_id
            ? intval(substr($lastVendor->vendor_id, 4))
            : 0;
    }

    public function model(array $row)
    {
        $now = Carbon::now();
        $user_id = Auth::id();

        if (empty($row['display_name'])) {
            return null;
        }
        // Increment vendor number for each row
        $this->currentNumber++;
        $vendorCode = 'VEN-' . str_pad($this->currentNumber, 3, '0', STR_PAD_LEFT);

        // Create Vendor
        $vendor = Tblvendor::create([
            'vendor_id'           => $vendorCode,
            'user_id'             => $user_id,
            'vendor_salutation'   => $row['salutation'] ?? null,
            'vendor_first_name'   => $row['first_name'] ?? null,
            'vendor_last_name'    => $row['last_name'] ?? null,
            'company_name'        => $row['company_name'] ?? null,
            'display_name'        => $row['display_name'] ?? null,
            'email'               => $row['emailid'] ?? null,
            'work_phone'          => $row['phone'] ?? null,
            'mobile'              => $row['mobilephone'] ?? null,
            'pan_number'          => $row['pan_number'] ?? null,
            'payment_terms'       => $row['payment_terms'] ?? null,
            'website'             => $row['website'] ?? null,
            'opening_balance' =>    $row['opening_balance'] !==' ' ? $row['opening_balance'] : null,
            'skype'               => $row['skype_identity'] ?? null,
            'department'          => $row['department'] ?? null,
            'designation'         => $row['designation'] ?? null,
            'facebook'            => $row['facebook'] ?? null,
            'twitter'             => $row['twitter'] ?? null,
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        // Create Billing
        TblBilling::create([
            'vendor_id'  => $vendor->id,
            'attention'  => $row['billing_attention'] ?? null,
            'address'    => $row['billing_address'] ?? null,
            'city'       => $row['billing_city'] ?? null,
            'state'      => $row['billing_state'] ?? null,
            'country'    => $row['billing_country'] ?? null,
            'zip_code'   => $row['billing_code'] ?? null,
            'phone'      => $row['billing_phone'] ?? null,
            'fax'        => $row['billing_fax'] ?? null,
            'created_at' => $now,
        ]);

        // Create Shipping
        TblShipping::create([
            'vendor_id'  => $vendor->id,
            'attention'  => $row['shipping_attention'] ?? null,
            'address'    => $row['shipping_address'] ?? null,
            'city'       => $row['shipping_city'] ?? null,
            'state'      => $row['shipping_state'] ?? null,
            'country'    => $row['shipping_country'] ?? null,
            'zip_code'   => $row['shipping_code'] ?? null,
            'phone'      => $row['shipping_phone'] ?? null,
            'fax'        => $row['shipping_fax'] ?? null,
            'created_at' => $now,
        ]);

        // Create Bank Details
        Tblbankdetails::create([
            'vendor_id'           => $vendor->id,
            'accont_number'      => $row['vendor_bank_account_number'] ?? null,
            'account_holder_name' => $row['vendor_bank_holder_name'] ?? null,
            'bank_name'           => $row['vendor_bank_name'] ?? null,
            'ifsc_code'           => $row['vendor_bank_ifsc_code'] ?? null,
            'created_at'          => $now,
        ]);

        return $vendor;
    }
}
