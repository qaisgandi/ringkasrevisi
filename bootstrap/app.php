<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\View\Compilers\BladeCompiler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBindings([
        BladeCompiler::class => function ($app) {
            return new BladeCompiler(
                $app->make('files'),
                '/tmp'
            );
        },
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Exact proxy addresses are loaded from config/security.php by AppServiceProvider.
        $middleware->trustProxies(at: []);
        $middleware->prepend(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->web(append: [\Illuminate\Session\Middleware\AuthenticateSession::class]);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'track.visit' => \App\Http\Middleware\TrackVisit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['current_password', 'old_password', 'password', 'password_confirmation']);
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (
            \Illuminate\Http\Exceptions\PostTooLargeException $e,
            Request $request
        ) {
            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation', 'current_password', 'old_password']))
                ->withErrors([
                    'pdf' => 'Ukuran berkas terlalu besar untuk diunggah. Batas maksimal server saat ini adalah '
                        . ini_get('post_max_size')
                        . '. Silakan unggah berkas yang lebih kecil atau hubungi admin untuk meningkatkan limit server.'
                ]);
        });
    })
    ->create();