@php
    use App\Models\BackendBaseModel as BBM;
@endphp

<div class="form-group {{ $errors->has($name) ? ' has-danger' : '' }}">
    <label>{{ __($label) }} @if (!isset($required)) <span class="text-danger">*</span> @endif </label>
    <select name="{{ $name }}" class="form-control {{ $errors->has($name) ? ' is-invalid' : '' }}">
        @isset($value)
            <option value="{{ BBM::STATUS_ACTIVE }}" {{ $value == BBM::STATUS_ACTIVE ? 'selected' : '' }}>
                {{ __(BBM::STATUS_ACTIVE_TEXT) }}</option>
            <option value="{{ BBM::STATUS_INACTIVE }}" {{ $value == BBM::STATUS_INACTIVE ? 'selected' : '' }}>
                {{ __(BBM::STATUS_INACTIVE_TEXT) }}</option>
        @else
            <option value="{{ BBM::STATUS_ACTIVE }}" {{ old($name) == BBM::STATUS_ACTIVE ? 'selected' : '' }}>
                {{ __(BBM::STATUS_ACTIVE_TEXT) }}</option>
            <option value="{{ BBM::STATUS_INACTIVE }}" {{ old($name) == BBM::STATUS_INACTIVE ? 'selected' : '' }}>
                {{ __(BBM::STATUS_INACTIVE_TEXT) }}</option>
        @endisset

    </select>
    @include('backend.includes.form-feedback', ['field' => $name])
</div>
