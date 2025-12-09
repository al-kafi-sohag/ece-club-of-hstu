@extends('backend.auth.layouts.master')

@section('title', 'Reset Password')

@section('content')
<div class="login-card bg-white shadow-lg rounded p-4 mx-auto">
    <h2 class="text-center fw-semibold mb-4 text-indigo">Reset Password</h2>

    <form method="POST" action="{{ route('backend.auth.rp.submit') }}" id="resetPasswordForm">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label fw-medium disabled">Email</label>
            <input id="email" type="email" name="email" readonly class="form-control" value="{{ $email }}">
            @include('backend.includes.form-feedback', ['field' => 'email'])
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-medium">Password <span class="text-danger">*</span></label>
            <input id="password" type="password" name="password" required
                   class="form-control" placeholder="Enter your password" autocomplete="new-password">
            @include('backend.includes.form-feedback', ['field' => 'password'])
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-medium">Confirm Password <span class="text-danger">*</span></label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="form-control" placeholder="Confirm your password" autocomplete="new-password">
            @include('backend.includes.form-feedback', ['field' => 'password_confirmation'])
            @include('backend.includes.form-feedback', ['field' => 'g-recaptcha-response'])
        </div>

        <!-- Forgot Password Link -->
        <div class="text-end mb-3">
            <a href="{{ route('backend.auth.login') }}" class="small text-indigo">Back to Login</a>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
            Reset Password
        </button>
    </form>
</div>

@endsection

@push('scripts')
@if(config('services.google_recaptcha.enabled') == '1')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.google_recaptcha.site_key') }}"></script>
    <script>
        $('#resetPasswordForm').submit(function(event) {
            event.preventDefault();

            grecaptcha.ready(function() {
                grecaptcha.execute("{{ config('services.google_recaptcha.site_key') }}", {action: 'login'}).then(function(token) {
                    if($("input[name='g-recaptcha-response']").length != 0){
                        $("input[name='g-recaptcha-response']").remove();
                    }
                    $('#resetPasswordForm').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                    $('#resetPasswordForm').unbind('submit').submit();
                });
            });
        });
    </script>
@endif
@endpush
