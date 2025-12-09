@isset($edit_data)
    <div class="form-group {{ $errors->has('order') ? ' has-danger' : '' }}">
        <label>{{ __('Order') }} @if (!isset($required)) <span class="text-danger">*</span> @endif</label>
        <select name="order" class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }}">
            @php
                $currentOrder = old('order', $edit_data->order);
                $maxOrder = $order_options->count() > 0 ? $order_options->max('order') : 0;
                $isCurrentLast = $currentOrder >= $maxOrder;
            @endphp
            <option value="1" {{ $currentOrder == 1 ? 'selected' : '' }}>
                {{ __('At the First') }}</option>
            @foreach ($order_options as $option_item)
                <option value="{{ $option_item->order }}" {{ $currentOrder == $option_item->order ? 'selected' : '' }}>
                    Before: {{ $option_item->$print_field }}</option>
            @endforeach
            @if ($order_options->count() > 0)
                <option value="{{ $maxOrder + 1 }}" {{ $isCurrentLast ? 'selected' : '' }}>
                    {{ __('At the Last') }}</option>
            @endif
        </select>
        @include('backend.includes.form-feedback', ['field' => 'order'])
    </div>
@else
    <div class="form-group {{ $errors->has('order') ? ' has-danger' : '' }}">
        <label>{{ __('Order') }} @if (!isset($required)) <span class="text-danger">*</span> @endif</label>
        <select name="order" class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }}">
            <option value="1" selected>{{ __('At the First') }}</option>
            @foreach ($order_options as $option_item)
                <option value="{{ $option_item->order }}" {{ old('order') == $option_item->order ? 'selected' : '' }}>
                    Before: {{ $option_item->$print_field ?: $option_item->order }}</option>
                @if ($loop->last)
                    <option value="{{ $option_item->order + 1 }}"
                        {{ old('order') == $option_item->order + 1 ? 'selected' : '' }}>
                        After: {{ $option_item->$print_field ?: $option_item->order }}</option>
                @endif
            @endforeach
            @if ($order_options->count() > 0)
                <option value="{{ $order_options->max('order') + 1 }}">{{ __('At the Last') }}</option>
            @endif
        </select>
        @include('backend.includes.form-feedback', ['field' => 'order'])
    </div>
@endisset
