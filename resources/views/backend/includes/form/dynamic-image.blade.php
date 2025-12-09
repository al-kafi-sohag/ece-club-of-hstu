@php
    $name = $name ?? 'image';
    $label = $label ?? 'UploadImage';
    $required = $required ?? true;
    $width = $width ?? 300;
    $height = $height ?? 300;
    $maxfilesize = $maxfilesize ?? 2;
@endphp

<div class="col-md-12">
    <div class="d-flex align-items-center">
        <div
            class="form-group  {{ $errors->has($name) ? ' has-danger' : '' }}">
            <label for="{{ $name }}">
                {{ $label }}
                @if ($required)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="file"
                class="form-control dynamic-image {{ $errors->has($name) ? 'is-invalid' : '' }}"
                id="{{ $name }}" name="{{ $name }}"
                @if (!empty($dataSrc)) data-src="{{ $dataSrc }}" @endif
                data-width="{{ $width }}"
                data-height="{{ $height }}" data-maxfilesize="{{ $maxfilesize }}"
                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml"
                {{ $required ? 'required' : '' }}>
        </div>
    </div>
    @include('backend.includes.form-feedback', [
        'field' => $name,
    ])
    <small class="text-muted">Image size should be less than 2MB. Image format
        should be
        JPEG, JPG, PNG</small>
</div>
