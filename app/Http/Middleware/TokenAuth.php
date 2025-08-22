<?php
// Simpan file ini di: app/Http/Middleware/TokenAuth.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Debug: Log middleware execution
        Log::info('TokenAuth middleware executed');
        Log::info('Current route: ' . $request->route()->getName());
        Log::info('Session token exists: ' . (session('token') ? 'YES' : 'NO'));

        // Periksa apakah token ada di session
        if (!session('token')) {
            Log::warning('No token in session, redirecting to login');
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu')
                ->withInput();
        }

        // Optional: Validasi token dengan API (jika diperlukan)
        // Untuk sekarang, kita hanya cek keberadaan token di session

        Log::info('Token found, allowing access');
        return $next($request);
    }
}
