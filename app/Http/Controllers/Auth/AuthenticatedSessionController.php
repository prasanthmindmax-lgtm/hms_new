<?php



namespace App\Http\Controllers\Auth;



use App\Http\Controllers\Controller;

use App\Http\Requests\Auth\LoginRequest;

use App\Providers\RouteServiceProvider;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;
use App\Models\ActivityLog;



class AuthenticatedSessionController extends Controller

{

    /**

     * Display the login view.

     */

    public function create(): View

    {

        return view('auth.login');

    }



    /**

     * Display the login view.

     */

    public function create1(): View

    {

        return view('auth.login');

    }



    /**

     * Handle an incoming authentication request.

     */

    public function store(LoginRequest $request): RedirectResponse

    {
        // dd($request);

        $request->authenticate();



        $request->session()->regenerate();

        // ── Capture & persist the real machine IP for this session ──
        // detectRealIp() resolves the actual LAN IP (e.g. 192.168.0.177) even
        // when accessed from localhost on XAMPP via gethostbyname(gethostname()).
        $clientIp = \App\Models\ActivityLog::detectRealIp();

        // Store in session — every subsequent log entry reads this value
        $request->session()->put('user_login_ip', $clientIp);
        $request->session()->put('user_login_at', now()->toDateTimeString());

        $lat = $request->filled('login_latitude') ? round((float) $request->input('login_latitude'), 7) : null;
        $lng = $request->filled('login_longitude') ? round((float) $request->input('login_longitude'), 7) : null;
        $acc = $request->filled('login_location_accuracy') ? round((float) $request->input('login_location_accuracy'), 2) : null;
        $geoStatus = $request->input('login_geo_status');
        if (! is_string($geoStatus) || $geoStatus === '') {
            $geoStatus = ($lat !== null && $lng !== null) ? 'granted' : null;
        }

        $loginExtra = [
            'login_ip' => $clientIp,
            'login_at' => now()->toDateTimeString(),
        ];
        if ($lat !== null && $lng !== null) {
            $loginExtra['latitude'] = $lat;
            $loginExtra['longitude'] = $lng;
            if ($acc !== null) {
                $loginExtra['location_accuracy_m'] = $acc;
            }
        }
        if ($geoStatus) {
            $loginExtra['geo_status'] = $geoStatus;
        }

        $desc = 'User logged in from IP: ' . $clientIp;
        if ($lat !== null && $lng !== null) {
            $desc .= ' | Coordinates: ' . $lat . ', ' . $lng;
        } elseif ($geoStatus === 'denied') {
            $desc .= ' | Location: not shared (permission denied)';
        } elseif ($geoStatus === 'timeout' || $geoStatus === 'unavailable') {
            $desc .= ' | Location: unavailable';
        }

        $url = '';
        if ($request->user()->role_id === 1) {
            $url = 'superadmin/dashboard';
        } elseif ($request->user()->role_id === 2) {
            $url = 'referral/dashboard';
        } elseif ($request->user()->role_id === 3) {
            $url = 'staff/dashboard';
        } elseif ($request->user()->role_id === 4) {
            $url = 'admin/dashboard';
        } elseif ($request->user()->role_id === 5) {
            $url = 'management/dashboard';
        } else {
            $url = 'login';
        }

        // Log the login event (session IP is now set — resolveIp() will pick it up)
        ActivityLog::log('Login', 'Auth', $desc, $loginExtra);

        return redirect()->intended($url);

    }



    /**

     * Destroy an authenticated session.

     */

    public function destroy(Request $request): RedirectResponse

    {
       // dd("sdfdsfsdfdsfdfds");

        // Log the logout event before session is destroyed
        ActivityLog::log('Logout', 'Auth', 'User logged out.');

        Auth::guard('web')->logout();



        $request->session()->invalidate();



        $request->session()->regenerateToken();



        return redirect('/');

    }

}

