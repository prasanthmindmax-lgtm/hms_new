<?php

namespace App\Listeners\UserActivity;

use App\Services\UserActivity\UserActivityService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;

class HandleUserActivityAuth
{
    public function __construct(
        protected UserActivityService $service
    ) {}

    public function onLogin(Login $event): void
    {
        $user = $event->user;
        if (! $user) {
            return;
        }
        $request = app(Request::class);
        $id = (int) $user->getAuthIdentifier();
        if ($id < 1) {
            return;
        }
        $fp = substr(sha1((string) session()->getId()), 0, 64);
        $this->service->onLogin(
            $id,
            $request->ip(),
            $request->userAgent(),
            $fp
        );
    }

    public function onLogout(Logout $event): void
    {
        $user = $event->user;
        if (! $user) {
            return;
        }
        $id = (int) $user->getAuthIdentifier();
        if ($id < 1) {
            return;
        }
        $this->service->onLogout($id);
    }
}
