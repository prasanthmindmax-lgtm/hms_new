<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GstR1Working extends Model
{
    protected $table = 'gst_r1_workings';

    protected $fillable = [
        'zone', 'state', 'branch', 'month', 'fin_year',
        'gst0_qty',  'gst0_taxable',
        'gst5_qty',  'gst5_taxable',  'gst5_cgst',  'gst5_sgst',
        'gst12_qty', 'gst12_taxable', 'gst12_cgst', 'gst12_sgst',
        'gst18_qty', 'gst18_taxable', 'gst18_cgst', 'gst18_sgst',
        'total_pharmacy', 'total_gst', 'exempt_sales',
        'total_turnover', 'collection', 'difference', 'source',
    ];

    protected $casts = [
        'gst0_qty'       => 'float',
        'gst0_taxable'   => 'float',
        'gst5_qty'       => 'float',
        'gst5_taxable'   => 'float',
        'gst5_cgst'      => 'float',
        'gst5_sgst'      => 'float',
        'gst12_qty'      => 'float',
        'gst12_taxable'  => 'float',
        'gst12_cgst'     => 'float',
        'gst12_sgst'     => 'float',
        'gst18_qty'      => 'float',
        'gst18_taxable'  => 'float',
        'gst18_cgst'     => 'float',
        'gst18_sgst'     => 'float',
        'total_pharmacy' => 'float',
        'total_gst'      => 'float',
        'exempt_sales'   => 'float',
        'total_turnover' => 'float',
        'collection'     => 'float',
        'difference'     => 'float',
    ];
}