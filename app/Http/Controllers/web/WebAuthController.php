<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\User;

class WebAuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request - DIRECT MODEL APPROACH
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        try {
            // DIRECT DATABASE AUTHENTICATION (NO API)
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Email tidak ditemukan'])
                            ->withInput($request->only('email'));
            }

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(['email' => 'Email atau password salah'])
                            ->withInput($request->only('email'));
            }

            // Check if user is active
            if (!$user->is_active) {
                return back()->withErrors(['email' => 'Akun Anda tidak aktif, hubungi administrator'])
                            ->withInput($request->only('email'));
            }

            // Save to session
            session([
                'token' => $user->api_token ?? $user->generateApiToken(),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_text' => $user->role_text, // Gunakan accessor dari model
                    'is_active' => $user->is_active,
                ],
            ]);

            Log::info('Direct login successful for user: ' . $user->email);

            return redirect()->route('dashboard')->with('success', 'Login berhasil! Selamat datang di SIMRS.');

        } catch (\Exception $e) {
            Log::error('Direct login error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Terjadi kesalahan sistem, coba lagi'])
                        ->withInput($request->only('email'));
        }
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $user = session('user');

        if (!$user) {
            Log::warning('No user in session, redirecting to login');
            return redirect()->route('login')->with('error', 'Session expired, silakan login kembali');
        }

        Log::info('Dashboard accessed by: ' . $user['email']);
        return view('dashboard', compact('user'));
    }

    /**
     * User profile page
     */
    public function profile()
    {
        $user = session('user');

        if (!$user) {
            return redirect()->route('login')->with('error', 'Session expired');
        }

        return view('auth.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        try {
            $sessionUser = session('user');
            $user = User::find($sessionUser['id']);

            if (!$user) {
                return back()->with('error', 'User tidak ditemukan');
            }

            // Update database
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update session
            $sessionUser['name'] = $request->name;
            $sessionUser['email'] = $request->email;
            session(['user' => $sessionUser]);

            return back()->with('success', 'Profil berhasil diupdate');

        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengupdate profil');
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'password.required' => 'Password baru harus diisi',
            'password.min' => 'Password baru minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        try {
            $sessionUser = session('user');
            $user = User::find($sessionUser['id']);

            if (!$user) {
                return back()->with('error', 'User tidak ditemukan');
            }

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah']);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return back()->with('success', 'Password berhasil diubah');

        } catch (\Exception $e) {
            Log::error('Change password error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah password');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $user = session('user');
        if ($user) {
            Log::info('User logged out: ' . $user['email']);
        }

        // Clear session
        session()->flush();

        return redirect()->route('login')->with('success', 'Logout berhasil. Terima kasih telah menggunakan SIMRS.');
    }
}
