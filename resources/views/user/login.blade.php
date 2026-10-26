@extends('layouts.auth')
@section('title', 'Selamat datang kembali.')
@section('description', 'Masuk untuk menjelajahi publikasi dan melanjutkan penelusuran Anda.')
@section('content')
<form action="{{ route('login.post') }}" method="POST" class="rk-form" data-submit-state>
@csrf
<x-auth-field name="username" label="Username" autocomplete="username" />
<x-auth-field name="password" label="Password" type="password" autocomplete="current-password"  />
<button type="submit" class="rk-btn">Masuk</button>
</form>
<div class="rk-auth-foot">Belum punya akun? <a href="{{ route('register') }}" class="rk-link">Daftar akun</a><p style="margin-top:16px;font-size:12px">Administrator? <a href="{{ route('admin.login') }}" class="rk-link">Masuk ke portal admin</a></p></div>
@endsection
