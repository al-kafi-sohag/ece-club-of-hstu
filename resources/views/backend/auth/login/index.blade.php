@extends('backend.auth.layouts.master')

@section('title', 'Admin Login')

@section('content')
<div class="login-card bg-white shadow-lg rounded p-4 mx-auto">
    <h2 class="text-center fw-semibold mb-4 text-indigo">Admin Login</h2>

    <form method="POST" action="{{ route('backend.auth.submit') }}" id="loginForm">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
            <input id="email" type="email" name="email" required autofocus
                   class="form-control" placeholder="Enter your email" value="{{ old('email') }}">

            @include('backend.includes.form-feedback', ['field' => 'email'])
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-medium">Password <span class="text-danger">*</span></label>
            <input id="password" type="password" name="password" required
                   class="form-control" placeholder="Enter your password">

            @include('backend.includes.form-feedback', ['field' => 'password'])
            @include('backend.includes.form-feedback', ['field' => 'g-recaptcha-response'])
        </div>

        <!-- Forgot Password Link -->
        <div class="text-end mb-3">
            <a href="{{ route('backend.auth.fp') }}" class="small text-indigo">Forgot Password?</a>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
            Login
        </button>
    </form>
</div>

@endsection

@push('scripts')
@if(config('services.google_recaptcha.enabled') == '1')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.google_recaptcha.site_key') }}"></script>
    <script>
        $('#loginForm').submit(function(event) {
            event.preventDefault();

            grecaptcha.ready(function() {
                grecaptcha.execute("{{ config('services.google_recaptcha.site_key') }}", {action: 'login'}).then(function(token) {
                    if($("input[name='g-recaptcha-response']").length != 0){
                        $("input[name='g-recaptcha-response']").remove();
                    }
                    $('#loginForm').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                    $('#loginForm').unbind('submit').submit();
                });
            });
        });
    </script>
@endif
@endpush
