<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('blade.compiler', function ($app) {
            return new BladeCompiler(
                $app['files'],
                '/tmp'
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TrustProxies::at(config('security.trusted_proxies', []));
        if ($this->app->environment('production')) {
            config(['app.debug' => false, 'session.secure' => true, 'session.http_only' => true]);
            URL::forceScheme('https');
        }
        $identity = fn ($request) => hash('sha256', strtolower(is_string($request->input('username')) ? $request->input('username') : '').'|'.$request->ip());
        RateLimiter::for('login', fn ($request) => [
            Limit::perMinute(5)->by('account:'.$identity($request)),
            Limit::perMinute(30)->by('ip:'.$request->ip()),
        ]);
        RateLimiter::for('register', fn ($request) => Limit::perHour(5)->by($request->ip()));
        RateLimiter::for('chatbot', fn ($request) => [
            Limit::perMinute(10)->by('minute:'.$request->user()->id),
            Limit::perHour(100)->by('hour:'.$request->user()->id),
        ]);
        RateLimiter::for('sensitive', fn ($request) => Limit::perMinute(5)->by($request->user()->id));
        RateLimiter::for('processing', fn ($request) => Limit::perMinute(3)->by($request->user()->id));
    }
}
