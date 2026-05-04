<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ArtBankController extends Controller
{
    /**
     * ART Donor Bank — Module (list view)
     */
    public function artBankModule()
    {
        $title = 'ART — Donor Bank';
        $admin = auth()->user();

        return view('modules.ART.art_bank_module', compact('title', 'admin'));
    }

    /**
     * ART Donor Bank — Donor Profile (detail view)
     */
    public function artBankDetail($donor_id = '')
    {
        $title  = 'ART — Donor Profile';
        $admin  = auth()->user();

        return view('modules.ART.art_bank_detail', compact('title', 'donor_id', 'admin'));
    }
}