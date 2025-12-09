@foreach ($errors->all() as $error)
    <div class="callout callout-danger mt-2">
        {{ $error }}
    </div>
@endforeach
