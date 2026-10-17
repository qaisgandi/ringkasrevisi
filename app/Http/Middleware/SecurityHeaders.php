<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $limit = match (true) {
            $request->is('login', 'admin/login', 'register') => 16 * 1024,
            $request->is('chatbot/send') => 256 * 1024,
            $request->is('admin/publications/*/save-extracted-text') => 5 * 1024 * 1024,
            default => null,
        };
        if ($limit !== null && max((int) $request->server('CONTENT_LENGTH', 0), strlen($request->getContent())) > $limit) {
            return response()->json(['message' => 'Ukuran permintaan melebihi batas.'], 413);
        }
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        // Existing Blade onclick handlers require inline scripts. No eval or third-party script hosts.
        $dev = app()->environment('local') && is_file(public_path('hot'));
        $scripts = $dev ? ' http://localhost:5173 http://127.0.0.1:5173' : '';
        $connect = $dev ? ' http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173' : '';
        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'; script-src 'self' 'unsafe-inline'{$scripts}; style-src 'self' 'unsafe-inline'{$scripts}; img-src 'self' data: blob:; font-src 'self' data:; connect-src 'self'{$connect}; worker-src 'self' blob:; frame-src 'self' blob:");
        }
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }
        if ($request->user() || $request->is('login', 'register', 'admin/login')) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}
