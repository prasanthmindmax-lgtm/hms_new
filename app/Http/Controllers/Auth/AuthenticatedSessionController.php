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

        $url = '';
        if ($request->user()->role_id === 1) {
            $url = 'superadmin/referral';
        } elseif ($request->user()->role_id === 2) {
            $url = 'referral/referral';
        } elseif ($request->user()->role_id === 3) {
            $url = 'staff/ticket';
        } elseif ($request->user()->role_id === 4) {
            $url = 'admin/ticket';
        } elseif ($request->user()->role_id === 5) {
            $url = 'management/ticket';
        } else {
            $url = 'login';
        }

        // Log the login event (session IP is now set — resolveIp() will pick it up)
        ActivityLog::log('Login', 'Auth',
            'User logged in from IP: ' . $clientIp,
            ['login_ip' => $clientIp, 'login_at' => now()->toDateTimeString()]
        );

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



        return redirect('/login');

    }

}

