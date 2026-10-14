<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     * Jika admin sudah masuk, redirect otomatis ke halaman beranda.
     */
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }
        return view('admin.login');
    }

    /**
     * Memproses data login admin.
     * Menggunakan data akun sementara (hardcoded).
     */
    public function login(Request $request)
    {
        // Validasi input form
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Validasi dengan Laravel Auth
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()
                    ->withInput($request->only('username'))
                    ->with('error', 'Akses ditolak. Akun Anda bukan Administrator. User Umum silakan login di /login.');
            }

            $request->session()->regenerate();

            // Menyimpan status login di session beserta detail profil (untuk backward compatibility)
            session([
                'admin_logged_in' => true,
                'admin_user_id' => $user->id,
                'admin_name' => $user->name,
                'admin_username' => $user->username,
                'admin_email' => $user->email,
            ]);

            // Redirect ke halaman dashboard
            return redirect()->route('admin.dashboard');
        }

        // Jika login gagal, kembalikan ke halaman login dengan input sebelumnya dan pesan error
        return back()
            ->withInput($request->only('username'))
            ->with('error', 'Username atau Password salah.');
    }

    /**
     * Menampilkan halaman dashboard.
     * Mengamankan halaman agar hanya dapat diakses oleh admin yang sudah login.
     */
    public function dashboard()
    {
        // Mengambil statistik riil dari database
        $publications = Publication::all();
        $totalPublications = $publications->count();
        $totalCategories = $publications->unique('category')->count();
        $recentPublications = $publications->sortByDesc('created_at')->take(5);

        // 1. Total ringkasan (status = Selesai)
        $totalSummaries = $publications->where('status', 'Selesai')->count();

        // 2. Publikasi per kategori
        $categories = [
            'Publikasi Umum',
            'Statistik Sosial',
            'Statistik Ekonomi',
            'Statistik Pertanian',
            'Statistik Industri',
            'Statistik Distribusi',
            'Statistik Lingkungan',
            'Sensus & Survei',
        ];
        $categoryData = [];
        foreach ($categories as $cat) {
            $categoryData[$cat] = $publications->where('category', $cat)->count();
        }

        // 3. Ringkasan per bulan (6 bulan terakhir)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m'); // e.g. "2026-07"
            $monthLabel = $date->format('M Y'); // e.g. "Jul 2026"
            
            $count = $publications->where('status', 'Selesai')->filter(function($pub) use ($monthKey) {
                return $pub->created_at->format('Y-m') === $monthKey;
            })->count();
            
            $monthlyData[$monthLabel] = $count;
        }

        return view('admin.dashboard', [
            'totalPublications' => $totalPublications,
            'totalCategories' => $totalCategories,
            'totalSummaries' => $totalSummaries,
            'categoryData' => $categoryData,
            'monthlyData' => $monthlyData,
            'recentPublications' => $recentPublications,
            'activeMenu' => 'dashboard'
        ]);
    }

    /**
     * Memproses logout admin.
     * Menghapus status login dari session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect kembali ke halaman login
        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Menampilkan Halaman Pengaturan Akun.
     */
    public function accountSettings()
    {
        $user = Auth::user();

        return view('account.settings', [
            'user' => $user,
            'activeMenu' => 'account_settings'
        ]);
    }

    /**
     * Memperbarui data profil akun.
     */
    public function updateAccountSettings(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $userId,
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh user lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
        ]);

        $oldUsername = $user->username;
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ]);

        // Sinkronisasi data session
        session([
            'admin_name' => $user->name,
            'admin_username' => $user->username,
            'admin_email' => $user->email,
        ]);

        // Catat aktivitas sistem
        \App\Models\Notification::create([
            'title' => 'Pengaturan Akun Diperbarui',
            'message' => "Profil admin '{$oldUsername}' diperbarui menjadi Nama: '{$user->name}', Username: '{$user->username}'.",
            'type' => 'info',
            'is_read' => false,
        ]);

        return back()->with('success', 'Profil akun berhasil diperbarui.');
    }

    /**
     * Memperbarui kata sandi akun.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'old_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password baru minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama yang Anda masukkan salah.']);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        // Catat aktivitas sistem
        \App\Models\Notification::create([
            'title' => 'Password Akun Diubah',
            'message' => "Password untuk akun admin '{$user->username}' berhasil diperbarui.",
            'type' => 'warning',
            'is_read' => false,
        ]);

        return back()->with('success', 'Password akun berhasil diperbarui.');
    }
}
