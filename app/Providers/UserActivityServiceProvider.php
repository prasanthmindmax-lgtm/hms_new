<?php

namespace App\Providers;

use App\Http\Middleware\UserActivityRequestLogger;
use App\Listeners\UserActivity\HandleUserActivityAuth;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class UserActivityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/user_activity.php',
            'user_activity'
        );
    }

    public function boot(): void
    {
        Event::listen(Login::class, [HandleUserActivityAuth::class, 'onLogin']);
        Event::listen(Logout::class, [HandleUserActivityAuth::class, 'onLogout']);

        $this->app['router']->pushMiddlewareToGroup('web', UserActivityRequestLogger::class);
    }
}
