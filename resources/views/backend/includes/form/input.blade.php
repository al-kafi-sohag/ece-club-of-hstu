@php
    $type = $type ?? 'text';
    $required = $required ?? true;
    $readonly = isset($readonly) ? true : false;
    $step = isset($step) ? $step : 0.01;
    $placeholder = isset($placeholder) ? $placeholder : '';
    $value = isset($value) ? $value : old($name);
@endphp

<div class="form-group {{ $errors->has($name) ? ' has-danger' : '' }}">
    <label>{{ $label }} @if ($required) <span class="text-danger">*</span> @endif</label>
    <input type="{{ $type ?? 'text' }}" name="{{ $name }}"
        class="form-control {{ $errors->has($name) ? ' is-invalid' : '' }}"
        placeholder="{{ $placeholder }}" value="{{ $value }}"
        {{ $required ? 'required' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $type == 'number' ? 'step="' . $step . '"' : '' }} >
    @include('backend.includes.form-feedback', ['field' => $name])
</div>
