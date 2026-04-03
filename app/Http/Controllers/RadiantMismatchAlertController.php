<?php

namespace App\Http\Controllers;

use App\Services\RadiantMismatchService;
use Illuminate\Http\Request;

class RadiantMismatchAlertController extends Controller
{
    public function __construct(protected RadiantMismatchService $service) {}

    /**
     * POST /radiant-cash-pickup/mismatch-alert
     * Manual trigger from the blade page.
     */
    public function sendAlert(Request $request)
    {
        $request->validate(['alert_date' => 'required|date']);

        $triggeredBy = auth()->user()->user_fullname
                    ?? auth()->user()->name
                    ?? 'Admin';

        $result = $this->service->checkAndAlert(
            $request->alert_date,
            $triggeredBy
        );

        if (!$result['found']) {
            return response()->json(['success' => false, 'message' => $result['message']]);
        }

        if (isset($result['email_error'])) {
            return response()->json(['success' => false, 'message' => $result['message']], 500);
        }

        return response()->json([
            'success'     => true,
            'all_matched' => $result['all_matched'],
            'total'       => $result['total'],
            'matched'     => $result['matched'],
            'mismatch'    => $result['mismatch'],
            'email_sent'  => $result['email_sent'],
            'recipients'  => $result['recipients'],
            'message'     => $result['message'],
        ]);
    }
}
