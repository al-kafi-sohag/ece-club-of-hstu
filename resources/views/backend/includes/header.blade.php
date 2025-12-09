<nav class="app-header navbar navbar-expand bg-secondary-subtle" id="navigation">
    <div class="container-fluid">
        <ul class="navbar-nav" role="navigation" aria-label="Navigation 1">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="fa fa-bars"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto" role="navigation" aria-label="Navigation 2">
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="fa fa-expand"></i>
                    <i data-lte-icon="minimize" class="fa fa-compress" style="display: none"></i>
                </a>
            </li>

            <li class="nav-item dropdown user-menu">
                <a href="javascript:void(0)" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="{{ auth('admin')->user()->profileImageUrl }}" class="user-image rounded-circle shadow"
                        alt="{{ auth('admin')->user()->name }}">
                    <span class="d-none d-md-inline">{{ auth('admin')->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <li class="user-header bg-primary">
                        <img src="{{ auth('admin')->user()->profileImageUrl }}" class="rounded-circle shadow" alt="User Image">
                        <p>
                            {{ auth('admin')->user()->name }}
                            <small>Member since {{ auth('admin')->user()->created_at->format('M Y') }}</small>
                        </p>
                    </li>
                    {{-- <li class="user-body">
                        <div class="row" >
                            <div class="col-4 text-center" >
                                <a href="#">Followers</a>
                            </div>
                            <div class="col-4 text-center" >
                                <a href="#">Sales</a>
                            </div>
                            <div class="col-4 text-center" >
                                <a href="#">Friends</a>
                            </div>
                        </div>
                    </li> --}}
                    <li class="user-footer">
                        <a href="{{ route('backend.profile.index') }}" class="btn btn-primary btn-flat">Profile</a>
                        <a href="{{ route('backend.auth.logout') }}" class="btn btn-danger btn-flat float-end">Sign out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
