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
use App\Models\IvfCycle;
use App\Models\Medication;
use App\Models\Monitoring;
use App\Models\AdverseEvent;
use App\Models\CycleSummary;
use Illuminate\Support\Facades\DB;


class IvfController extends Controller
{
    public function store(Request $request)
{
    DB::transaction(function() use ($request) {
        // Step 3.1: Insert Patient
        $patient = Patient::create([
            'patient_name' => $request->patientName,
            'mrn' => $request->mrn,
            'dob' => $request->dob,
            'age' => $request->age,
            'referring_doctor' => $request->refDr
        ]);

        // Step 3.2: Insert IVF Cycle
        $cycle = IvfCycle::create([
            'patient_id' => $patient->id,
            'cycle_no' => $request->cycleNo,
            'start_date' => $request->startDate,
            'estimated_trigger_date' => $request->estTrigger,
            'protocol' => $request->protocol,
            'stimulation_drugs' => $request->stimulationDrugs,
            'planned_days' => $request->cyclesDays
        ]);

        // Step 3.3: Insert Medications
        if ($request->has('drug')) {
            foreach ($request->drug as $key => $drugName) {
                Medication::create([
                    'cycle_id' => $cycle->id,
                    'drug_name' => $drugName,
                    'dose' => $request->dose[$key],
                    'route' => $request->route[$key],
                    'frequency' => $request->freq[$key],
                    'start_date' => $request->start[$key],
                    'stop_date' => $request->stop[$key]
                ]);
            }
        }

        // Step 3.4: Insert Monitoring Entries
        if ($request->has('mon_date')) {
            foreach ($request->mon_date as $key => $date) {
                Monitoring::create([
                    'cycle_id' => $cycle->id,
                    'monitor_date' => $date,
                    'usg_follicles' => $request->mon_usg[$key],
                    'e2_level' => $request->mon_e2[$key],
                    'endometrium_thickness' => $request->mon_endo[$key],
                    'notes' => $request->mon_notes[$key]
                ]);
            }
        }

        // Step 3.5: Insert Adverse Events
        AdverseEvent::create([
            'cycle_id' => $cycle->id,
            'ohss_risk' => $request->ohssRisk,
            'weight' => $request->weight,
            'bp' => $request->bp,
            'adverse_notes' => $request->adverse
        ]);

        // Step 3.6: Insert Summary / Plan
        CycleSummary::create([
            'cycle_id' => $cycle->id,
            'plan_notes' => $request->plan
        ]);
    });

    return redirect()->back()->with('success', 'Data saved successfully!');
}
   
}
