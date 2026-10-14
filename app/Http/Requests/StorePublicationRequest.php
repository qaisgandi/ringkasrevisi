<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     * Menggunakan autentikasi berbasis session manual.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'admin';
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:Publikasi Umum,Statistik Sosial,Statistik Ekonomi,Statistik Pertanian,Statistik Industri,Statistik Distribusi,Statistik Lingkungan,Sensus & Survei',
            'release_date' => 'required|date',
            'pdf' => 'required|file|mimes:pdf|max:25600', // 25600 KB = 25 MB
        ];
    }

    /**
     * Dapatkan pesan kesalahan khusus untuk aturan validasi yang ditentukan.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul publikasi wajib diisi.',
            'title.string' => 'Judul publikasi harus berupa teks.',
            'title.max' => 'Judul publikasi maksimal 255 karakter.',
            
            'category.required' => 'Kategori publikasi wajib diisi.',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            
            'release_date.required' => 'Tanggal rilis publikasi wajib diisi.',
            'release_date.date' => 'Format tanggal rilis tidak valid.',
            
            'pdf.required' => 'Berkas PDF publikasi wajib diunggah.',
            'pdf.file' => 'Unggahan harus berupa file berkas.',
            'pdf.mimes' => 'Format berkas harus berupa dokumen .pdf.',
            'pdf.max' => 'Ukuran berkas PDF maksimal 25 MB.',
        ];
    }
}
