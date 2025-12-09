<div class="form-group color-pickr-parent {{ $errors->has("$name") ? ' has-danger' : '' }}">
    <span>{{ __("$label") }} <span class="text-danger">*</span></span>
    <div class="color-pickr"></div>
    <input type="hidden" id="" name="{{$name}}"
    @isset($value) value="{{ old($name) ?: $value }}" @else value="{{ old($name) }}" @endisset
    >
    @include('backend.includes.form-feedback', ['field' => $name])
</div>
