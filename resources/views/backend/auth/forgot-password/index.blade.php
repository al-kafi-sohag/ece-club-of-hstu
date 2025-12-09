@extends('backend.auth.layouts.master')

@section('title', 'Forgot Password')

@section('content')
<div class="login-card bg-white shadow-lg rounded p-4 mx-auto">
    <h2 class="text-center fw-semibold mb-4 text-indigo">Forgot Password</h2>

    <form method="POST" action="{{ route('backend.auth.fp.submit') }}" id="forgotPasswordForm">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
            <input id="email" type="email" name="email" required autofocus
                   class="form-control" placeholder="Enter your email" value="{{ old('email') }}">

            @include('backend.includes.form-feedback', ['field' => 'email'])
            @include('backend.includes.form-feedback', ['field' => 'g-recaptcha-response'])
        </div>


        <div class="text-end mb-3">
            <a href="{{ route('backend.auth.login') }}" class="small text-indigo">Back to Login</a>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
            Submit
        </button>
    </form>
</div>

@endsection

@push('scripts')
@if(config('services.google_recaptcha.enabled') == '1')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.google_recaptcha.site_key') }}"></script>
    <script>
        $('#forgotPasswordForm').submit(function(event) {
            event.preventDefault();

            grecaptcha.ready(function() {
                grecaptcha.execute("{{ config('services.google_recaptcha.site_key') }}", {action: 'login'}).then(function(token) {
                    if($("input[name='g-recaptcha-response']").length != 0){
                        $("input[name='g-recaptcha-response']").remove();
                    }
                    $('#forgotPasswordForm').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                    $('#forgotPasswordForm').unbind('submit').submit();
                });
            });
        });
    </script>
@endif
@endpush
