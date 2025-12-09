<div class="form-group {{ $errors->has($name) ? ' has-danger' : '' }}">
    <div class="d-flex justify-content-left align-items-center">
        <label>{{ __($label) }} @if ($required ?? false) <span class="text-danger">*</span> @endif </label>

    </div>
    <div class="input-group">
        <select name="{{ $name }}" class="form-select {{ $errors->has($name) ? ' is-invalid' : '' }} {{ $select2 ?? '' }}">
            <option value="" hidden>{{ $placeholder ?? '' }}</option>
            @isset($value)
                @foreach ($options as $key => $option)
                    <option value="{{ $key }}" {{ $value == $key ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            @else
                @foreach ($options as $key => $option)
                    <option value="{{ $key }}" {{ old($name) == $key ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            @endisset

        </select>
        @if (isset($addRoute))
        <a href="{{ route($addRoute) }}" target="_self" class="btn btn-sm btn-success input-group-text d-flex align-items-center" title="Add New">
            <i class="fa fa-plus"></i>
        </a>
    @endif
    </div>
    @include('backend.includes.form-feedback', ['field' => $name])
</div>
