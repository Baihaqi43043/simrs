<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class WebRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // Debug middleware execution
        Log::info('WebRoleMiddleware executed', [
            'required_role' => $role,
            'current_route' => $request->route()->getName()
        ]);

        // Check if user is authenticated via session
        if (!session('token') || !session('user')) {
            Log::warning('No session found in WebRoleMiddleware');
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $user = session('user');

        // Debug user info
        Log::info('WebRoleMiddleware user check', [
            'user_role' => $user['role'] ?? 'NOT_SET',
            'user_email' => $user['email'] ?? 'NOT_SET',
            'is_active' => $user['is_active'] ?? 'NOT_SET'
        ]);

        // Check if user is active
        if (!$user['is_active']) {
            Log::warning('User is not active', ['user_email' => $user['email']]);
            return redirect()->route('login')
                ->with('error', 'Akun Anda tidak aktif');
        }

        // Admin can access everything
        if ($user['role'] === 'admin') {
            Log::info('Admin access granted');
            return $next($request);
        }

        // Check specific role
        $allowedRoles = explode('|', $role);

        Log::info('Role check', [
            'user_role' => $user['role'],
            'required_roles' => $allowedRoles,
            'access_granted' => in_array($user['role'], $allowedRoles)
        ]);

        if (!in_array($user['role'], $allowedRoles)) {
            Log::warning('Access denied', [
                'user_role' => $user['role'],
                'required_roles' => $allowedRoles,
                'route' => $request->route()->getName()
            ]);

            return redirect()->back()
                ->with('error', "Anda tidak memiliki akses untuk halaman ini. Role Anda: {$user['role']}, Required: {$role}");
        }

        Log::info('Access granted');
        return $next($request);
    }
}
