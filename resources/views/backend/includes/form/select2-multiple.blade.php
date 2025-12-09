<div class="form-group {{ $errors->has($name) ? ' has-danger' : '' }}">
    <div class="d-flex justify-content-between align-items-center">
        <label>{{ __($label) }} @if ($required ?? false) <span class="text-danger">*</span> @endif </label>

    </div>
    <div class="input-group">
        <select name="{{ $name }}[]" id="{{ $name }}" class="form-select {{ $select2 ?? '' }} {{ $errors->has($name) ? ' is-invalid' : '' }}" multiple>
            @if (isset($options) && $options->count() > 0)
                @foreach ($options as $option)
                    <option value="{{ $option->id }}"
                        @if (isset($value) && is_array($value) && in_array($option->id, $value)) selected
                        @elseif (old($name) && in_array($option->id, old($name))) selected @endif>
                        {{ $option->title ?? $option->name }}
                    </option>
                @endforeach
            @endif
        </select>
        @if (isset($addRoute))
            <a href="{{ route($addRoute) }}" target="_self" class="btn btn-sm btn-success input-group-text d-flex align-items-center" title="Add New">
                <i class="fa fa-plus"></i>
            </a>
        @endif
    </div>
    @include('backend.includes.form-feedback', ['field' => $name])
</div>
