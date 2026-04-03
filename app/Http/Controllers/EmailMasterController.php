<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TblPoEmail;

class EmailMasterController extends Controller
{
    /* ── Page ── */
    public function index()
    {
        $admin = auth()->user();
        
        // All main menus from the menus table
        $menus = DB::table('menus')
            ->where('main_menu', 1)
            ->orderBy('menu_name')
            ->get(['id', 'menu_name']);

        $records = TblPoEmail::orderBy('id')->get();

        $stats = [
            'total'    => $records->count(),
            'active'   => $records->where('status', 1)->count(),
            'inactive' => $records->where('status', 0)->count(),
        ];

        // Keyed map passed to JS as window.EM_RECORDS for edit without reload
        $recordsMap = $records->keyBy('id')->map(function ($r) {
            $cc    = $this->decodeJson($r->cc_emails);
            $menus = $this->decodeJson($r->menu_type);
            return [
                'id'            => $r->id,
                'label'         => $r->label,
                'to_email'      => $r->to_email ?: $r->email,
                'cc_emails'     => $cc,
                'menu_types'    => $menus,
                'mobile_number' => $r->mobile_number,
                'status'        => (int) $r->status,
                'created_by'    => $r->created_by,
                'updated_at'    => $r->updated_at ? $r->updated_at->format('d M Y') : '',
            ];
        });

        return view('superadmin.email_master', compact('records', 'menus', 'stats', 'admin', 'recordsMap'));
    }

    /* ── Store / Update ── */
    public function store(Request $request)
    {
        $request->validate([
            'to_email'      => 'required|email',
            'label'         => 'nullable|string|max:120',
            'cc_emails'     => 'nullable|array',
            'cc_emails.*'   => 'email',
            'menu_types'    => 'nullable|array',
            'menu_types.*'  => 'string',
            'mobile_number' => 'nullable|string|max:20',
            'status'        => 'required|in:0,1',
        ]);

        $admin = auth()->user();
        $cc    = array_values(array_filter($request->input('cc_emails', [])));
        $menus = array_values(array_filter($request->input('menu_types', [])));

        $data = [
            'label'         => $request->label,
            'to_email'      => $request->to_email,
            'email'         => $request->to_email,
            'cc_emails'     => json_encode($cc),
            'menu_type'     => json_encode($menus),
            'mobile_number' => $request->mobile_number,
            'status'        => (int) $request->status,
            'user_id'       => $admin->id,
            'created_by'    => $admin->user_fullname ?? $admin->name,
        ];

        $id = $request->id;

        if ($id) {
            unset($data['user_id'], $data['created_by']);
            TblPoEmail::where('id', $id)->update($data);
            $msg = 'Email config updated successfully!';
        } else {
            $data['created_at'] = now();
            TblPoEmail::create($data);
            $msg = 'Email config added successfully!';
        }

        // Reload menus for stats
        $menus = DB::table('menus')->where('main_menu', 1)->orderBy('menu_name')->get(['id', 'menu_name']);

        return response()->json([
            'success' => true,
            'message' => $msg,
            'records' => $this->recordsJson(),
            'stats'   => $this->statsJson(),
            'menus'   => $menus,
        ]);
    }

    /* ── Delete ── */
    public function destroy($id)
    {
        TblPoEmail::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email config deleted.',
            'records' => $this->recordsJson(),
            'stats'   => $this->statsJson(),
        ]);
    }

    /* ── Toggle Status ── */
    public function toggleStatus($id)
    {
        $rec = TblPoEmail::findOrFail($id);
        $rec->status = $rec->status ? 0 : 1;
        $rec->save();

        return response()->json([
            'success' => true,
            'status'  => $rec->status,
            'message' => $rec->status ? 'Activated.' : 'Deactivated.',
            'stats'   => $this->statsJson(),
        ]);
    }

    /* ── Helpers ── */
    private function recordsJson()
    {
        return TblPoEmail::orderBy('id')->get()->map(function ($r) {
            $cc    = $this->decodeJson($r->cc_emails);
            $menus = $this->decodeJson($r->menu_type);

            return [
                'id'            => $r->id,
                'label'         => $r->label,
                'to_email'      => $r->to_email ?: $r->email,
                'cc_emails'     => $cc,
                'menu_types'    => $menus,
                'mobile_number' => $r->mobile_number,
                'status'        => $r->status,
                'created_by'    => $r->created_by,
                'updated_at'    => $r->updated_at ? $r->updated_at->format('d M Y') : '',
            ];
        });
    }

    private function statsJson()
    {
        $recs = TblPoEmail::get();
        return [
            'total'    => $recs->count(),
            'active'   => $recs->where('status', 1)->count(),
            'inactive' => $recs->where('status', 0)->count(),
        ];
    }

    private function decodeJson($val): array
    {
        if (is_array($val)) return $val;
        if (!$val) return [];
        $decoded = json_decode($val, true);
        return is_array($decoded) ? $decoded : [];
    }
}
