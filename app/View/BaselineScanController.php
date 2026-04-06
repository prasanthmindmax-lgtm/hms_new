<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Patient;
use App\Models\BaselineScan;
use App\Models\OvaryCyst;

use Illuminate\Support\Facades\DB;



class BaselineScanController extends Controller
{
    public function store(Request $request)
    {
        DB::transaction(function() use ($request) {

            // 1. Save patient
            $patient = Patient::firstOrCreate(
                ['patient_code' => $request->patientId],
                [
                    'full_name'  => $request->patientName,
                    'age'        => $request->age,
                    'consultant' => $request->consultant
                ]
            );

            // 2. Save scan
            $scan = $patient->scans()->create([
                'scan_date'      => $request->scanDate,
                'cycle_no'       => $request->cycleNo,
                'uterus_size'    => $request->uterusSize,
                'endo_thickness' => $request->endoThickness,
                'uterus_notes'   => $request->uterusNotes,

                'right_dims'     => $request->right_dims,
                'right_afc'      => $request->right_afc ?? 0,
                'left_dims'      => $request->left_dims,
                'left_afc'       => $request->left_afc ?? 0,
                'afc_total'      => ($request->right_afc ?? 0) + ($request->left_afc ?? 0),

                'pelvic_notes'   => $request->pelvicNotes,
                'fsh'            => $request->fsh,
                'lh'             => $request->lh,
                'amh'            => $request->amh,
                'e2'             => $request->e2,
                'progesterone'   => $request->prog,

                'suitable'       => $request->suitable,
                'protocol'       => $request->protocol,
                'medications'    => $request->meds,
                'notes'          => $request->notes,

                'doctor_sign'    => $request->doctorSign,
                'patient_ack'    => $request->patientAck,
            ]);

            // 3. Save cysts
            if($request->cysts){
                foreach($request->cysts['right'] ?? [] as $cyst){
                    $scan->cysts()->create([
                        'side'        => 'right',
                        'description' => $cyst['desc'] ?? null,
                        'size'        => $cyst['size'] ?? null
                    ]);
                }
                foreach($request->cysts['left'] ?? [] as $cyst){
                    $scan->cysts()->create([
                        'side'        => 'left',
                        'description' => $cyst['desc'] ?? null,
                        'size'        => $cyst['size'] ?? null
                    ]);
                }
            }
        });

        return redirect()->back()->with('message', 'Baseline IVF Scan saved successfully!');
    }

}
