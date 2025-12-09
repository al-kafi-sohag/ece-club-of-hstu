<div class="container-fluid my-3">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ __($details->title) }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="fa fa-plus"></i>
                    <i data-lte-icon="collapse" class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-lte-toggle="card-maximize">
                    <i data-lte-icon="maximize" class="fa fa-expand"></i>
                    <i data-lte-icon="minimize" class="fa fa-compress"></i>
                </button>
            </div>
        </div>
        <form action="{{ route('backend.single_page.sp.form.store', $details->page_key) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        @if ($details->form_data)
                            @foreach (json_decode($details->form_data) as $k => $fd)
                                @php
                                    $a = $fd->field_key;
                                    // dd($details, json_decode($details->form_data), $a);
                                    $count = 0;
                                @endphp

                                @if ($fd->type == 'text')
                                    <div class="form-group {{ $errors->has($fd->field_key) ? ' has-danger' : '' }}">
                                        <label for="{{ $fd->field_key }}">{{ $fd->field_name }}</label>
                                        @if (isset($fd->required) && $fd->required == 'required')
                                            <span class="text-danger">*</span>
                                        @endif
                                        <input type="text" name="{{ $fd->field_key }}" id="{{ $fd->field_key }}"
                                            class="form-control title {{ $errors->has($fd->field_key) ? ' is-invalid' : '' }}"
                                            value="{{ json_decode($details->saved_data)->$a ?? old($fd->field_key) }}">
                                        @include('backend.includes.form-feedback', [
                                            'field' => $fd->field_key,
                                        ])
                                    </div>
                                @elseif($fd->type == 'number')
                                    <div class="form-group {{ $errors->has($fd->field_key) ? ' has-danger' : '' }}">
                                        <label for="{{ $fd->field_key }}">{{ $fd->field_name }}</label>
                                        @if (isset($fd->required) && $fd->required == 'required')
                                            <span class="text-danger">*</span>
                                        @endif
                                        <input type="number" name="{{ $fd->field_key }}" id="{{ $fd->field_key }}"
                                            class="form-control title {{ $errors->has($fd->field_key) ? ' is-invalid' : '' }}"
                                            value="{{ json_decode($details->saved_data)->$a ?? old($fd->field_key) }}">
                                        @include('backend.includes.form-feedback', [
                                            'field' => $fd->field_key,
                                        ])
                                    </div>
                                @elseif($fd->type == 'url')
                                    <div class="form-group {{ $errors->has($fd->field_key) ? ' has-danger' : '' }}">
                                        <label for="{{ $fd->field_key }}">{{ $fd->field_name }}</label>
                                        @if (isset($fd->required) && $fd->required == 'required')
                                            <span class="text-danger">*</span>
                                        @endif
                                        <input type="url" name="{{ $fd->field_key }}" id="{{ $fd->field_key }}"
                                            class="form-control title {{ $errors->has($fd->field_key) ? ' is-invalid' : '' }}"
                                            value="{{ json_decode($details->saved_data)->$a ?? old($fd->field_key) }}">
                                        @include('backend.includes.form-feedback', [
                                            'field' => $fd->field_key,
                                        ])
                                    </div>
                                @elseif($fd->type == 'textarea')
                                    <div class="form-group {{ $errors->has($fd->field_key) ? ' has-danger' : '' }}">
                                        <label for="{{ $fd->field_key }}">{{ $fd->field_name }}</label>
                                        @if (isset($fd->required) && $fd->required == 'required')
                                            <span class="text-danger">*</span>
                                        @endif
                                        <textarea name="{{ $fd->field_key }}" id="{{ $fd->field_key }}"
                                            class="form-control tinymce-editor title {{ $errors->has($fd->field_key) ? ' is-invalid' : '' }}">
                                            {!! json_decode($details->saved_data)->$a ?? old($fd->field_key) !!}
                                        </textarea>
                                        @include('backend.includes.form-feedback', [
                                            'field' => $fd->field_key,
                                        ])
                                    </div>
                                @elseif($fd->type == 'image')
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="form-group  {{ $errors->has($fd->field_key) ? ' has-danger' : '' }}">
                                                <label for="{{ $fd->field_key }}">
                                                    {{ $fd->field_name }}
                                                    @if (isset($fd->required) && $fd->required == 'required')
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="file"
                                                    class="form-control dynamic-image {{ $errors->has($fd->field_key) ? 'is-invalid' : '' }}"
                                                    id="{{ $fd->field_key }}" name="{{ $fd->field_key }}"
                                                    @if (!empty(json_decode($details->saved_data)) && isset(json_decode($details->saved_data)->$a)) data-src="{{ json_decode($details->saved_data)->$a }}" @endif
                                                    {{-- data-src="{{ $admin->profileImageUrl }}" --}} data-width="{{ $fd->width }}"
                                                    data-height="{{ $fd->height }}" data-maxfilesize="2"
                                                    accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml">
                                            </div>
                                        </div>
                                        @include('backend.includes.form-feedback', [
                                            'field' => $fd->field_key,
                                        ])
                                        <small class="text-muted">Image size should be less than 2MB. Image format
                                            should be
                                            JPEG, JPG, PNG</small>
                                    </div>
                                @elseif($fd->type == 'email')
                                    <div class="form-group {{ $errors->has($fd->field_key) ? ' has-danger' : '' }}">
                                        <label for="{{ $fd->field_key }}">{{ $fd->field_name }}</label>
                                        @if (isset($fd->required) && $fd->required == 'required')
                                            <span class="text-danger">*</span>
                                        @endif
                                        <input type="email" name="{{ $fd->field_key }}" id="{{ $fd->field_key }}"
                                            class="form-control  {{ $errors->has($fd->field_key) ? 'is-invalid' : '' }}"
                                            value="{{ json_decode($details->saved_data)->$a ?? old($fd->field_key) }}">
                                        @include('backend.includes.form-feedback', [
                                            'field' => $fd->field_key,
                                        ])
                                    </div>
                                @elseif($fd->type == 'option')
                                    <div class="form-group {{ $errors->has($fd->field_key) ? ' has-danger' : '' }}">
                                        <label for="{{ $fd->field_key }}">{{ $fd->field_name }}</label>
                                        @if (isset($fd->required) && $fd->required == 'required')
                                            <span class="text-danger">*</span>
                                        @endif
                                        <select name="{{ $fd->field_key }}" id="{{ $fd->field_key }}"
                                            class="form-control  {{ $errors->has($fd->field_key) ? 'is-invalid' : '' }}">
                                            @foreach ($fd->option_data as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (isset(json_decode($details->saved_data)->$a) &&
                                                            (json_decode($details->saved_data)->$a == $value || old($fd->field_key) == $value)) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @include('backend.includes.form-feedback', [
                                            'field' => $fd->field_key,
                                        ])
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100">Update</button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card documentation-card">
                            <div class="card-header">
                                <h3 class="card-title fw-bold">Documentation</h3>
                            </div>
                            <div class="card-body">
                                <p>
                                    {!! __(json_decode($details->documentation)->details ?? '') !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
