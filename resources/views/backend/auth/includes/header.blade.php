<header class="d-flex justify-content-between align-items-center px-4 py-3 bg-white shadow-sm header">
    <!-- Logo -->
    <div class="d-flex align-items-center">
        <a href="{{ config('app.url') }}">
            <img src="{{ asset(config('app.public_logo')) }}" alt="Logo">
        </a>
    </div>

    <!-- Buttons -->
    <div class="d-flex buttons">
        @if (!auth()->guard('admin')->check())
        <a href="{{ route('backend.auth.login') }}" class="btn btn-indigo text-white">
            Login
        </a>
        <a href="{{ route('backend.auth.fp') }}" class="ms-3 btn btn-indigo text-white">
            Forgot Password
        </a>
        @else
        <a href="{{ route('backend.dashboard.index') }}" class="btn btn-indigo text-white">
            Dashboard
        </a>
        <a href="{{ route('backend.auth.logout') }}" class="ms-3 btn btn-indigo text-white">
            Logout
        </a>

        @endif
    </div>
</header>
