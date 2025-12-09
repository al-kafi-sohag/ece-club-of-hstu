<div class="form-group {{ $errors->has($name) ? ' has-danger' : '' }}">

    <label>{{ __( $label) }} @if (!isset($required)) <span class="text-danger">*</span> @endif</label>

    <textarea
        name="{{ $name }}"
        class="form-control {{ $errors->has($name) ? ' is-invalid' : '' }} {{ $class ?? '' }}"
        {{ $required ?? '' }}
        @isset($placeholder) placeholder="{{ $placeholder }}" @endisset
        @isset($rows) rows="{{ $rows }}" @endisset
    > {{ $value ?? old($name) }} </textarea>

    @include('backend.includes.form-feedback', ['field' => $name])
</div>
