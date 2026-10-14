<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Publication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    /**
     * Tampilkan halaman login user.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('user.login');
    }

    /**
     * Proses login user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'user') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()
                    ->withInput($request->only('username'))
                    ->with('error', 'Akun Administrator tidak dapat login di sini. Silakan login melalui Portal Admin (/admin/login).');
            }

            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Selamat datang kembali, ' . ($user->name ?: $user->username) . '!');
        }

        return back()
            ->withInput($request->only('username'))
            ->with('error', 'Username atau password salah.');
    }

    /**
     * Tampilkan halaman register user.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('user.register');
    }

    /**
     * Proses register user baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->input('username'), // Default name to username
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'user',
        ]);

        // Catat aktivitas sistem (Notifikasi untuk admin)
        \App\Models\Notification::create([
            'title' => 'User Baru Mendaftar',
            'message' => "User baru '{$user->username}' ({$user->email}) berhasil mendaftar ke sistem.",
            'type' => 'success',
            'is_read' => false,
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Akun berhasil didaftarkan! Selamat datang di RINGKAS.');
    }

    /**
     * Proses logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Tampilkan beranda user umum.
     */
    public function beranda(Request $request)
    {
        // 8 Kategori Resmi Publikasi BPS yang sama dengan Admin
        $officialCategories = [
            'Publikasi Umum',
            'Statistik Sosial',
            'Statistik Ekonomi',
            'Statistik Pertanian',
            'Statistik Industri',
            'Statistik Distribusi',
            'Statistik Lingkungan',
            'Sensus & Survei',
        ];

        $query = Publication::with('aiResult');

        // Filter berdasarkan kategori jika ditentukan
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Pencarian publikasi
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%')
                  ->orWhere('year', 'like', '%' . $search . '%')
                  ->orWhere('region', 'like', '%' . $search . '%');
            });
        }

        // Urutkan dari yang paling baru diunggah
        $publications = $query->latest()->get();

        // Hitung total publikasi per masing-masing dari 8 kategori resmi
        $categoryCounts = [];
        foreach ($officialCategories as $cat) {
            $categoryCounts[$cat] = Publication::where('category', $cat)->count();
        }

        $totalAllPublications = Publication::count();

        return view('user.home', [
            'publications' => $publications,
            'officialCategories' => $officialCategories,
            'categoryCounts' => $categoryCounts,
            'totalAllPublications' => $totalAllPublications,
            'selectedCategory' => $request->input('category'),
            'searchQuery' => $request->input('search'),
        ]);
    }

    /**
     * Tampilkan halaman profil user.
     */
    public function profile()
    {
        return view('user.profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Memperbarui profil user umum.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh user lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $userData = [
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->input('password'));
        }

        $user->update($userData);

        return back()->with('success', 'Profil akun berhasil diperbarui.');
    }

    /**
     * Tampilkan detail halaman sebuah publikasi untuk user umum.
     */
    public function showPublication(Publication $publication)
    {
        return view('user.show_publication', [
            'publication' => $publication,
        ]);
    }
}
