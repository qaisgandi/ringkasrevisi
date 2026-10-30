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
                    ->with('error', 'Jenis akun ini tidak dapat digunakan pada portal pengguna.');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, ' . ($user->name ?: $user->username) . '!');
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

        // Catat registrasi untuk statistik dashboard. Login otomatis di bawah
        // tidak perlu dicatat lagi sebagai event terpisah.
        app(\App\Services\VisitTracker::class)->record($request, \App\Services\VisitTracker::REGISTER, null, $user->id);
        $request->attributes->set('visit.login.already_recorded', true);

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

        $query = Publication::query()->summarized()->with('aiResult');

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

        // Hitung total publikasi per masing-masing dari 8 kategori resmi (hanya yang sudah diringkas)
        $categoryCounts = [];
        foreach ($officialCategories as $cat) {
            $categoryCounts[$cat] = Publication::query()->summarized()->where('category', $cat)->count();
        }

        $totalAllPublications = Publication::query()->summarized()->count();

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
        if (! $publication->isSummarized()) {
            return redirect()
                ->route('home')
                ->with('error', 'Publikasi ini belum tersedia untuk umum karena belum selesai diringkas.');
        }

        return view('user.show_publication', [
            'publication' => $publication,
        ]);
    }

    /** Unduh PDF publikasi dan catat event unduhan untuk dashboard admin. */
    public function downloadPublication(Request $request, Publication $publication)
    {
        abort_unless($publication->isSummarized(), 404);

        $fileService = app(\App\Services\PublicationFileService::class);
        $isBlob = $fileService->isTrustedBlobUrl((string) $publication->pdf_path);
        abort_unless($isBlob || preg_match('/\Apublications\/[a-zA-Z0-9_.-]+\.pdf\z/i', (string) $publication->pdf_path), 404);
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        if (!$isBlob && !$disk->exists($publication->pdf_path)) {
            return back()->with('error', 'Berkas PDF tidak ditemukan.');
        }

        try {
            app(\App\Services\VisitTracker::class)->record(
                $request,
                \App\Services\VisitTracker::DOWNLOAD,
                $publication->id
            );
        } catch (\Throwable $e) {
            // Kegagalan pencatatan tidak boleh memblokir unduhan.
        }

        $filename = \Illuminate\Support\Str::slug(substr($publication->title, 0, 60)) . '.pdf';

        if ($isBlob) {
            return redirect()->away($publication->pdf_path);
        }

        return $disk->download($publication->pdf_path, $filename);
    }
}
