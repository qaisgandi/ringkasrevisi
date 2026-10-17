@extends('layouts.auth')
@section('title', 'Mulai memahami data.')
@section('description', 'Buat akun untuk membaca publikasi, ringkasan, dan menggunakan asisten tanya jawab.')
@section('content')
<form action="{{ route('register.post') }}" method="POST" class="rk-form" data-submit-state>
@csrf
<x-auth-field name="username" label="Username" autocomplete="username" />
<x-auth-field name="email" label="Alamat email" type="email" autocomplete="email" />
<x-auth-field name="password" label="Password" type="password" autocomplete="new-password" minimum="12" hint="Gunakan minimal 12 karakter." />
<x-auth-field name="password_confirmation" label="Konfirmasi password" type="password" autocomplete="new-password" minimum="12" />
<button type="submit" class="rk-btn">Buat akun</button>
</form>
<div class="rk-auth-foot">Sudah punya akun? <a href="{{ route('login') }}" class="rk-link">Masuk</a></div>
@endsection
