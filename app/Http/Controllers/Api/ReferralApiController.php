<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\doctordetails;
use App\Models\meetingdetails;
use App\Models\patientdetails;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * REST API for superadmin referral module data (doctors, meetings, patients, lookups).
 * Does not modify SuperAdminController or Blade views.
 */
class ReferralApiController extends Controller
{
    /**
     * Issue a Sanctum personal access token (same credentials as web: username + password).
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => [trans('auth.failed')],
            ]);
        }

        if (isset($user->active_status) && (int) $user->active_status === 1) {
            return response()->json([
                'message' => "You don't have access to this resource.",
            ], 403);
        }

        $user->tokens()->where('name', 'referral-api')->delete();
        $token = $user->createToken('referral-api')->plainTextToken;

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->sanitizeUser($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->sanitizeUser($request->user()));
    }

    /**
     * Doctor list — same access rules as SuperAdminController::fetch.
     *
     * Query: ref_doctor_id (optional filter by id)
     */
    public function doctors(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = $this->doctorBaseQuery();

        $err = $this->applyDoctorAccessScope($query, $user);
        if ($err !== null) {
            return response()->json(['message' => $err['message']], $err['code']);
        }

        if ($request->filled('ref_doctor_id')) {
            $query->where('ref_doctor_details.id', $request->input('ref_doctor_id'));
        }

        $rows = $query->orderBy('ref_doctor_details.created_at', 'desc')->get();

        return response()->json(['data' => $rows]);
    }

    /**
     * Single doctor with joins (aligned with list columns, not doctordetailsid legacy join).
     */
    public function doctor(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $query = $this->doctorBaseQuery()->where('ref_doctor_details.id', $id);

        $err = $this->applyDoctorAccessScope($query, $user);
        if ($err !== null) {
            return response()->json(['message' => $err['message']], $err['code']);
        }

        $row = $query->first();
        if (! $row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $row]);
    }

    /** All meeting log rows (same as meetingallviews). */
    public function meetings(): JsonResponse
    {
        $rows = DB::table('ref_meeting_log')->orderBy('id', 'desc')->get();

        return response()->json(['data' => $rows]);
    }

    /** All patient referral rows (same as patientallviews). */
    public function patients(): JsonResponse
    {
        $rows = DB::table('ref_patient_details')->orderBy('id', 'desc')->get();

        return response()->json(['data' => $rows]);
    }

    /** Branch / location list for filters (matches superadmin.referral passing locations). */
    public function locations(): JsonResponse
    {
        $locations = TblLocationModel::orderBy('name', 'asc')->get();

        return response()->json(['data' => $locations]);
    }

    /** Zones from tblzones. */
    public function zones(): JsonResponse
    {
        $zones = TblZonesModel::orderBy('name', 'asc')->get(['id', 'name']);

        return response()->json(['data' => $zones]);
    }

    /** Marketers (role_id = 2) — same idea as marketernamesurls. */
    public function marketers(): JsonResponse
    {
        $rows = DB::table('users')
            ->where('role_id', 2)
            ->orderBy('user_fullname', 'asc')
            ->select('id', 'user_fullname', 'role_id', 'username')
            ->get();

        return response()->json(['data' => $rows]);
    }

    /** Legacy branches table (used by some referral JS endpoints). */
    public function branchesLegacy(): JsonResponse
    {
        $rows = DB::table('branches')->orderBy('branch_name', 'asc')->get();

        return response()->json(['data' => $rows]);
    }

    /** Legacy zones table. */
    public function zonesLegacy(): JsonResponse
    {
        $rows = DB::table('zones')->get();

        return response()->json(['data' => $rows]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function doctorBaseQuery()
    {
        return doctordetails::query()
            ->join('tbl_locations', 'ref_doctor_details.city', '=', 'tbl_locations.id')
            ->join('tblzones', 'tbl_locations.zone_id', '=', 'tblzones.id')
            ->join('users', 'users.username', '=', 'ref_doctor_details.empolyee_name')
            ->select(
                'tbl_locations.name as location_name',
                'tblzones.name as zone_name',
                'ref_doctor_details.*',
                'user_fullname'
            );
    }

    /**
     * @return array{message: string, code: int}|null
     */
    private function applyDoctorAccessScope($query, User $user): ?array
    {
        $accessLog = DB::table('access_log')->where('employee_id', $user->id)->first();

        if (! $accessLog) {
            return ['message' => 'Access privileges not found', 'code' => 403];
        }

        $access_limit = (int) $accessLog->access_limits;
        $user_name    = $user->username;

        switch ($access_limit) {
            case 1:
                break;
            case 2:
                $query->where(function ($q) use ($user, $user_name) {
                    $q->whereIn('ref_doctor_details.empolyee_name', function ($subQuery) use ($user) {
                        $subQuery->select('username')
                            ->from('users')
                            ->where('reporting_manager', $user->id);
                    })->orWhere('ref_doctor_details.empolyee_name', $user_name);
                });
                break;
            case 3:
                $query->whereIn('ref_doctor_details.empolyee_name', function ($subQuery) use ($user) {
                    $subQuery->select('username')->from('users')->where('id', $user->id);
                });
                break;
            default:
                return ['message' => 'Unauthorized access', 'code' => 403];
        }

        return null;
    }

    private function sanitizeUser(User $user): array
    {
        return [
            'id'            => $user->id,
            'username'      => $user->username ?? null,
            'name'          => $user->name ?? null,
            'email'         => $user->email ?? null,
            'role'          => $user->role ?? null,
            'role_id'       => $user->role_id ?? null,
            'access_heads'  => $user->access_heads ?? null,
        ];
    }
}
