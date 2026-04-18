<?php

namespace App\Http\Controllers;

use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocationMasterController extends Controller
{
    /** Main page — same tables VendorController uses: tblzones + tbl_locations (branch under zone). */
    public function index(Request $request)
    {
        $admin = auth()->user();
        $zones = TblZonesModel::orderBy('name')->get();

        return view('superadmin.location_master', compact('admin', 'zones'));
    }

    /** DataTables JSON — locations with zone name */
    public function locationsList(Request $request)
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);
        $search = $request->input('search.value', '');
        $zoneFilter = $request->input('zone_id');

        $makeBase = function () use ($search, $zoneFilter) {
            $q = DB::table('tbl_locations as l')
                ->join('tblzones as z', 'l.zone_id', '=', 'z.id')
                ->select('l.id', 'l.name', 'l.zone_id', 'l.status', 'l.level', 'z.name as zone_name');

            if ($zoneFilter !== null && $zoneFilter !== '') {
                $q->where('l.zone_id', (int) $zoneFilter);
            }
            if ($search !== '') {
                $q->where(function ($w) use ($search) {
                    $w->where('l.name', 'like', "%{$search}%")
                        ->orWhere('z.name', 'like', "%{$search}%");
                });
            }

            return $q;
        };

        $total = DB::table('tbl_locations')->count();
        $filtered = $makeBase()->count();
        $rows = $makeBase()
            ->orderBy('z.name')
            ->orderBy('l.name')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function ($row, $i) use ($start) {
            $st = (int) ($row->status ?? 0);
            $statusLabel = $st === 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
            $lvl = (int) ($row->level ?? 1);
            if ($lvl !== 2) {
                $lvl = 1;
            }
            $levelLabel = $lvl === 2
                ? '<span class="badge rounded-pill" style="background:#ecfdf5;color:#047857;font-weight:600;">Level 2</span>'
                : '<span class="badge rounded-pill" style="background:#eef2ff;color:#4338ca;font-weight:600;">Level 1</span>';

            return [
                'DT_RowIndex' => $start + $i + 1,
                'id' => $row->id,
                'zone_name' => e($row->zone_name),
                'name' => e($row->name),
                'name_plain' => $row->name,
                'level' => $levelLabel,
                'status' => $statusLabel,
                'status_raw' => $st,
                'zone_id' => $row->zone_id,
                'action' => '',
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function storeZone(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()->all()], 422);
        }

        $id = TblZonesModel::create(['name' => trim($request->name)])->id;

        return response()->json([
            'success' => true,
            'message' => 'Zone created.',
            'zone' => ['id' => $id, 'name' => trim($request->name)],
        ]);
    }

    public function updateZone(Request $request, int $id)
    {
        $zone = TblZonesModel::find($id);
        if (! $zone) {
            return response()->json(['success' => false, 'message' => 'Zone not found.'], 404);
        }

        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()->all()], 422);
        }

        $zone->name = trim($request->name);
        $zone->save();

        return response()->json(['success' => true, 'message' => 'Zone updated.']);
    }

    public function destroyZone(int $id)
    {
        $zone = TblZonesModel::find($id);
        if (! $zone) {
            return response()->json(['success' => false, 'message' => 'Zone not found.'], 404);
        }

        $cnt = TblLocationModel::where('zone_id', $id)->count();
        if ($cnt > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete: {$cnt} branch(es) still use this zone. Remove or reassign them first.",
            ], 422);
        }

        $zone->delete();

        return response()->json(['success' => true, 'message' => 'Zone deleted.']);
    }

    public function storeLocation(Request $request)
    {
        $v = Validator::make($request->all(), [
            'zone_id' => 'required|integer|exists:tblzones,id',
            'name' => 'required|string|max:191',
            'status' => 'nullable|in:0,1',
            'level' => 'required|in:1,2',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()->all()], 422);
        }

        TblLocationModel::create([
            'zone_id' => (int) $request->zone_id,
            'name' => trim($request->name),
            'status' => (int) ($request->status ?? 1),
            'level' => (int) $request->level,
        ]);

        return response()->json(['success' => true, 'message' => 'Branch (location) created.']);
    }

    public function showLocation(int $id)
    {
        $loc = TblLocationModel::find($id);
        if (! $loc) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'location' => [
                'id' => $loc->id,
                'name' => $loc->name,
                'zone_id' => $loc->zone_id,
                'status' => (int) ($loc->status ?? 1),
                'level' => (int) ($loc->level ?? 1),
            ],
        ]);
    }

    public function updateLocation(Request $request, int $id)
    {
        $loc = TblLocationModel::find($id);
        if (! $loc) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $v = Validator::make($request->all(), [
            'zone_id' => 'required|integer|exists:tblzones,id',
            'name' => 'required|string|max:191',
            'status' => 'nullable|in:0,1',
            'level' => 'required|in:1,2',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()->all()], 422);
        }

        $loc->zone_id = (int) $request->zone_id;
        $loc->name = trim($request->name);
        $loc->status = (int) ($request->status ?? 1);
        $loc->level = (int) $request->level;
        $loc->save();

        return response()->json(['success' => true, 'message' => 'Branch updated.']);
    }

    public function destroyLocation(int $id)
    {
        $loc = TblLocationModel::find($id);
        if (! $loc) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        try {
            $loc->delete();
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete: this branch may be referenced by bills or other records.',
            ], 422);
        }

        return response()->json(['success' => true, 'message' => 'Branch deleted.']);
    }
}
