@props(['name', 'label', 'type' => 'text', 'autocomplete' => null, 'minimum' => null, 'hint' => null])
<div class="rk-field"><label for="{{ $name }}">{{ $label }}</label><div @class(['rk-password' => $type === 'password'])>
<input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" required @if($type !== 'password') value="{{ old($name) }}" @endif @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif @if($minimum) minlength="{{ $minimum }}" @endif aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}" @if($hint || $errors->has($name)) aria-describedby="{{ $name }}-hint" @endif>
@if($type === 'password')<button type="button" class="toggle-password-btn" data-target="{{ $name }}" aria-label="Tampilkan {{ strtolower($label) }}" aria-pressed="false">Tampilkan</button>@endif</div>
@if($errors->has($name))<p class="rk-field-error" id="{{ $name }}-hint">{{ $errors->first($name) }}</p>@elseif($hint)<p class="rk-form-note" id="{{ $name }}-hint">{{ $hint }}</p>@endif</div>
