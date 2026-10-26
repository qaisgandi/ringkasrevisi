@if(session('success'))
    <div class="rk-alert rk-alert-success" role="status">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="rk-alert rk-alert-error" role="alert">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="rk-alert rk-alert-error" role="alert" tabindex="-1">
        <strong>Periksa kembali isian Anda.</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
