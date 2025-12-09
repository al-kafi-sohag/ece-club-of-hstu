@php
    $currentPageSlug = request()->route() ? request()->route()->parameter('page_slug') : null;
@endphp
<aside class="app-sidebar shadow bg-stale-gray" data-bs-theme="light">
    <div class="sidebar-brand">
        <a class="brand-link" href="{{ config('app.url') }}">
            <img src="{{ asset(config('app.public_logo')) }}" alt="{{ config('app.name') }} Logo" class="brand-image">
        </a>
    </div>

    <div class="sidebar-wrapper" data-overlayscrollbars="host">
        <div class="os-size-observer">
            <div class="os-size-observer-listener"></div>
        </div>
        <div class="" data-overlayscrollbars-viewport="scrollbarHidden overflowXHidden overflowYHidden">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu"
                    data-accordion="false" aria-label="Navigation">

                    {{-- Dashboard  --}}
                    @include('backend.includes.navbar.menu_buttons', [
                        'menuItems' => [
                            [
                                'pageSlug' => 'dashboard',
                                'routeName' => 'backend.dashboard.index',
                                'iconClass' => 'fa fa-tachometer-alt',
                                'label' => 'Dashboard',
                            ],
                        ],
                    ])

                    @include('backend.includes.navbar.menu_buttons', [
                        'menuItems' => [
                            [
                                'pageSlug' => 'profile',
                                'routeName' => 'backend.profile.index',
                                'iconClass' => 'fa fa-user',
                                'label' => 'Profile',
                            ],
                        ],
                    ])


                </ul>
            </nav>
        </div>
    </div>
</aside>
