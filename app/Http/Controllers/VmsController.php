<?php

namespace App\Http\Controllers;

use App\Models\VmsVisitor;
use App\Models\VmsQrCode;
use App\Models\VmsBlacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VmsController extends Controller
{
    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function settings(): array
    {
        return DB::table('vms_settings')->pluck('value', 'key')->toArray();
    }

    private function isBlacklisted(string $phone): bool
    {
        return VmsBlacklist::where('visitor_phone', $phone)->where('is_active', true)->exists();
    }

    // ─── DASHBOARD ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $today = now()->toDateString();

        $stats = [
            'total_today'      => VmsVisitor::whereDate('created_at', $today)->count(),
            'active_inside'    => VmsVisitor::where('status', 'inside')->count(),
            'pending_approvals'=> VmsVisitor::where('status', 'pending')->count(),
            'pharma_today'     => VmsVisitor::whereDate('created_at', $today)->where('visitor_type', 'pharma')->count(),
            'non_pharma_today' => VmsVisitor::whereDate('created_at', $today)->where('visitor_type', 'non_pharma')->count(),
            'blacklist_alerts' => VmsBlacklist::where('is_active', true)->count(),
        ];

        $activeVisitors = VmsVisitor::where('status', 'inside')
            ->orderBy('entry_time', 'asc')
            ->take(10)
            ->get();

        $hourlyData = [];
        for ($h = 8; $h <= 18; $h++) {
            $hourlyData[] = VmsVisitor::whereDate('created_at', $today)
                ->whereRaw('HOUR(created_at) = ?', [$h])
                ->count();
        }

        $typeData = [
            'pharma'           => VmsVisitor::whereDate('created_at', $today)->where('visitor_type', 'pharma')->count(),
            'non_pharma'       => VmsVisitor::whereDate('created_at', $today)->where('visitor_type', 'non_pharma')->count(),
            'patient_relative' => VmsVisitor::whereDate('created_at', $today)->where('visitor_type', 'patient_relative')->count(),
            'others'           => VmsVisitor::whereDate('created_at', $today)->whereNotIn('visitor_type', ['pharma','non_pharma','patient_relative'])->count(),
        ];

        $settings = $this->settings();

        return view('vms.dashboard', compact('stats', 'activeVisitors', 'hourlyData', 'typeData', 'settings'));
    }

    // ─── APPROVALS ────────────────────────────────────────────────────────────

    public function approvals(Request $request)
    {
        $query = VmsVisitor::where('status', 'pending')->latest();
        if ($request->filled('type')) $query->where('visitor_type', $request->type);
        $visitors = $query->paginate(20);
        $settings = $this->settings();
        return view('vms.approvals', compact('visitors', 'settings'));
    }

    public function approve(Request $request, $id)
    {
        $visitor = VmsVisitor::findOrFail($id);
        $visitor->update([
            'status'      => 'inside',
            'entry_time'  => now(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'badge_number'=> 'B-' . str_pad($id, 4, '0', STR_PAD_LEFT),
        ]);
        return response()->json(['success' => true, 'message' => 'Visitor approved and entry recorded.']);
    }

    public function reject(Request $request, $id)
    {
        $visitor = VmsVisitor::findOrFail($id);
        $visitor->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);
        return response()->json(['success' => true, 'message' => 'Visitor rejected.']);
    }

    // ─── ACTIVE VISITORS ─────────────────────────────────────────────────────

    public function active(Request $request)
    {
        $settings = $this->settings();
        $maxMinutes = (int)($settings['max_visit_duration'] ?? 60);
        $visitors = VmsVisitor::where('status', 'inside')
            ->orderBy('entry_time', 'asc')
            ->get();
        return view('vms.active', compact('visitors', 'settings', 'maxMinutes'));
    }

    public function checkout(Request $request, $id)
    {
        $visitor = VmsVisitor::findOrFail($id);
        $visitor->update([
            'status'    => 'checked_out',
            'exit_time' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Visitor checked out.']);
    }

    // ─── HISTORY ─────────────────────────────────────────────────────────────

    public function history(Request $request)
    {
        $query = VmsVisitor::whereIn('status', ['checked_out', 'rejected', 'approved', 'inside'])->latest();

        if ($request->filled('search'))
            $query->where(function($q) use ($request) {
                $q->where('visitor_name', 'like', '%'.$request->search.'%')
                  ->orWhere('visitor_phone', 'like', '%'.$request->search.'%')
                  ->orWhere('company_name', 'like', '%'.$request->search.'%');
            });

        if ($request->filled('type'))   $query->where('visitor_type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('date'))   $query->whereDate('created_at', $request->date);

        $visitors = $query->paginate(25);
        $settings = $this->settings();
        return view('vms.history', compact('visitors', 'settings'));
    }

    // ─── PHARMA VENDORS ───────────────────────────────────────────────────────

    public function pharma(Request $request)
    {
        $query = VmsVisitor::where('visitor_type', 'pharma')->latest();
        if ($request->filled('search')) $query->where('company_name', 'like', '%'.$request->search.'%');
        if ($request->filled('date'))   $query->whereDate('created_at', $request->date);
        $visitors = $query->paginate(25);

        $topCompanies = VmsVisitor::where('visitor_type', 'pharma')
            ->select('company_name', DB::raw('count(*) as total'))
            ->groupBy('company_name')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $settings = $this->settings();
        return view('vms.pharma', compact('visitors', 'topCompanies', 'settings'));
    }

    // ─── NON-PHARMA VENDORS ───────────────────────────────────────────────────

    public function nonPharma(Request $request)
    {
        $query = VmsVisitor::where('visitor_type', 'non_pharma')->latest();
        if ($request->filled('search')) $query->where('company_name', 'like', '%'.$request->search.'%');
        if ($request->filled('date'))   $query->whereDate('created_at', $request->date);
        $visitors = $query->paginate(25);
        $settings = $this->settings();
        return view('vms.non-pharma', compact('visitors', 'settings'));
    }

    // ─── BLACKLIST ────────────────────────────────────────────────────────────

    public function blacklist(Request $request)
    {
        $query = VmsBlacklist::latest();
        if ($request->filled('search'))
            $query->where('visitor_name', 'like', '%'.$request->search.'%')
                  ->orWhere('visitor_phone', 'like', '%'.$request->search.'%');
        $blacklisted = $query->paginate(20);
        $settings = $this->settings();
        return view('vms.blacklist', compact('blacklisted', 'settings'));
    }

    public function storeBlacklist(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:255',
            'reason'       => 'required|string',
        ]);

        VmsBlacklist::create([
            'visitor_name'   => $request->visitor_name,
            'visitor_phone'  => $request->visitor_phone,
            'company_name'   => $request->company_name,
            'visitor_type'   => $request->visitor_type,
            'reason'         => $request->reason,
            'blacklisted_by' => auth()->id(),
            'blacklisted_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Added to blacklist.']);
    }

    public function removeBlacklist($id)
    {
        VmsBlacklist::findOrFail($id)->update(['is_active' => false]);
        return response()->json(['success' => true, 'message' => 'Removed from blacklist.']);
    }

    // ─── QR MANAGEMENT ────────────────────────────────────────────────────────

    public function qrManagement()
    {
        $qrCodes = VmsQrCode::withCount('visitors')->latest()->get();
        $settings = $this->settings();
        return view('vms.qr-management', compact('qrCodes', 'settings'));
    }

    public function createQr(Request $request)
    {
        $request->validate([
            'label'    => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'branch'   => 'nullable|string|max:255',
        ]);

        $qr = VmsQrCode::create([
            'label'      => $request->label,
            'location'   => $request->location,
            'branch'     => $request->branch,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'qr' => $qr, 'url' => $qr->register_url]);
    }

    public function toggleQr($id)
    {
        $qr = VmsQrCode::findOrFail($id);
        $qr->update(['is_active' => !$qr->is_active]);
        return response()->json(['success' => true, 'is_active' => $qr->is_active]);
    }

    public function deleteQr($id)
    {
        VmsQrCode::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ─── REPORTS ─────────────────────────────────────────────────────────────

    public function reports(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $pharmaByCompany = VmsVisitor::where('visitor_type', 'pharma')
            ->whereYear('created_at', $year)->whereMonth('created_at', $mon)
            ->select('company_name', DB::raw('count(*) as total'))
            ->groupBy('company_name')
            ->orderByDesc('total')->take(8)->get();

        $byDoctor = VmsVisitor::whereYear('created_at', $year)->whereMonth('created_at', $mon)
            ->whereNotNull('person_to_meet')
            ->select('person_to_meet', DB::raw('count(*) as total'))
            ->groupBy('person_to_meet')
            ->orderByDesc('total')->take(8)->get();

        $daily = VmsVisitor::whereYear('created_at', $year)->whereMonth('created_at', $mon)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')->get();

        $totalMonth = VmsVisitor::whereYear('created_at', $year)->whereMonth('created_at', $mon)->count();
        $avgDuration = VmsVisitor::whereYear('created_at', $year)->whereMonth('created_at', $mon)
            ->whereNotNull('exit_time')->whereNotNull('entry_time')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, entry_time, exit_time)) as avg_mins')
            ->value('avg_mins') ?? 0;

        $topPharma = VmsVisitor::where('visitor_type','pharma')
            ->whereYear('created_at',$year)->whereMonth('created_at',$mon)
            ->select('company_name', DB::raw('count(*) as total'))
            ->groupBy('company_name')->orderByDesc('total')->first();

        $repeatRate = 0;
        $totalVisitors = VmsVisitor::whereYear('created_at', $year)->whereMonth('created_at', $mon)
            ->select('visitor_phone', DB::raw('count(*) as cnt'))->groupBy('visitor_phone')
            ->havingRaw('cnt > 1')->count();
        $allVisitors = VmsVisitor::whereYear('created_at', $year)->whereMonth('created_at', $mon)->count();
        if ($allVisitors > 0) $repeatRate = round(($totalVisitors / $allVisitors) * 100);

        $settings = $this->settings();

        return view('vms.reports', compact(
            'pharmaByCompany', 'byDoctor', 'daily', 'month',
            'totalMonth', 'avgDuration', 'topPharma', 'repeatRate', 'settings'
        ));
    }

    // ─── SETTINGS ────────────────────────────────────────────────────────────

    public function settings_view()
    {
        $settings = $this->settings();
        return view('vms.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $keys = [
            'hospital_name', 'default_branch', 'max_visit_duration',
            'otp_enabled', 'auto_approve', 'doctors_list', 'departments_list',
        ];
        foreach ($keys as $key) {
            DB::table('vms_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $request->input($key), 'updated_at' => now()]
            );
        }
        return response()->json(['success' => true, 'message' => 'Settings saved.']);
    }

    // ─── PUBLIC: VISITOR REGISTRATION ────────────────────────────────────────

    public function showRegister(string $uuid)
    {
        $qr = VmsQrCode::where('uuid', $uuid)->where('is_active', true)->firstOrFail();
        $qr->increment('scan_count');
        $settings = $this->settings();
        $doctors = array_filter(array_map('trim', explode("\n", $settings['doctors_list'] ?? '')));
        $departments = array_filter(array_map('trim', explode("\n", $settings['departments_list'] ?? '')));
        return view('vms.register', compact('qr', 'settings', 'doctors', 'departments'));
    }

    public function storeRegister(Request $request, string $uuid)
    {
        $qr = VmsQrCode::where('uuid', $uuid)->where('is_active', true)->firstOrFail();

        $request->validate([
            'visitor_name'  => 'required|string|max:255',
            'visitor_phone' => 'required|string|max:20',
            'visitor_type'  => 'required|string',
            'purpose'       => 'required|string',
        ]);

        $blacklisted = $this->isBlacklisted($request->visitor_phone);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $file->store('vms/photos', 'public');
        }

        $settings = $this->settings();
        $autoApprove = ($settings['auto_approve'] ?? '0') === '1';
        $status = $blacklisted ? 'rejected' : ($autoApprove ? 'inside' : 'pending');

        $visitor = VmsVisitor::create([
            'qr_code_id'        => $qr->id,
            'visitor_name'      => $request->visitor_name,
            'visitor_phone'     => $request->visitor_phone,
            'visitor_email'     => $request->visitor_email,
            'visitor_type'      => $request->visitor_type,
            'company_name'      => $request->company_name,
            'purpose'           => $request->purpose,
            'person_to_meet'    => $request->person_to_meet,
            'department'        => $request->department,
            'appointment_time'  => $request->appointment_time,
            'equipment_carried' => $request->equipment_carried,
            'id_type'           => $request->id_type,
            'id_number'         => $request->id_number,
            'photo'             => $photoPath,
            'declaration_agreed'=> $request->boolean('declaration'),
            'branch'            => $qr->branch ?? ($settings['default_branch'] ?? 'Main Hospital'),
            'status'            => $status,
            'entry_time'        => ($status === 'inside') ? now() : null,
        ]);

        if ($blacklisted) {
            return view('vms.thankyou', [
                'blacklisted' => true,
                'settings'    => $settings,
            ]);
        }

        return view('vms.thankyou', [
            'visitor'     => $visitor,
            'blacklisted' => false,
            'settings'    => $settings,
        ]);
    }

    // ─── AJAX: dashboard stats refresh ────────────────────────────────────────
    public function ajaxStats()
    {
        $today = now()->toDateString();
        return response()->json([
            'active_inside'     => VmsVisitor::where('status', 'inside')->count(),
            'pending_approvals' => VmsVisitor::where('status', 'pending')->count(),
            'total_today'       => VmsVisitor::whereDate('created_at', $today)->count(),
        ]);
    }
}
