@extends('layouts.auth')
@section('title', 'Portal administrator.')
@section('description', 'Masuk dengan akun administrator untuk mengelola publikasi dan hasil ringkasan.')
@section('content')
<form action="{{ route('admin.login.post') }}" method="POST" class="rk-form" data-submit-state>
@csrf
<x-auth-field name="username" label="Username" autocomplete="username" />
<x-auth-field name="password" label="Password" type="password" autocomplete="current-password"  />
<button type="submit" class="rk-btn">Masuk</button>
</form>
<div class="rk-auth-foot">Pembaca publikasi? <a href="{{ route('login') }}" class="rk-link">Masuk sebagai pengguna</a></div>
@endsection
