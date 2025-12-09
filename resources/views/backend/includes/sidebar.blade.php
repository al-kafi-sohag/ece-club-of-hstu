@php
    $currentPageSlug = request()->route() ? request()->route()->parameter('page_slug') : null;
@endphp
<aside class="app-sidebar shadow bg-stale-gray" data-bs-theme="light">
    <div class="sidebar-brand">
        <a class="brand-link" href="{{ config('app.url') }}">
            <img src="{{ get_app_setting('logo') }}" alt="{{ config('app.name') }} Logo" class="brand-image">
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

                    {{-- Profile --}}
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

                    {{-- Admin management --}}
                    <li
                        class="nav-item @if (request()->routeIs('backend.admin_management.*')) menu-open @endif
                        @if (!check_access_by_route_name('backend.admin_management.admin_management')) d-none @endif">
                        <a href="#" class="nav-link align-items-center m-1">
                            <i class="fa fa-users"></i>
                            <p>
                                Admin Management
                                <i class="nav-arrow fa fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" role="navigation" aria-label="Navigation 15"
                            style="box-sizing: border-box; @if (request()->routeIs('backend.admin_management.*')) display: block; @else display: none; @endif ">

                            @include('backend.includes.navbar.menu_buttons', [
                                'menuItems' => [
                                    [
                                        'pageSlug' => 'admin_management',
                                        'routeName' => 'backend.admin_management.admin.admin_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Admins',
                                    ],
                                    [
                                        'pageSlug' => 'role_management',
                                        'routeName' => 'backend.admin_management.role.role_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Roles',
                                    ],
                                    [
                                        'pageSlug' => 'permission_management',
                                        'routeName' => 'backend.admin_management.permission.permission_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Permission',
                                    ],
                                ],
                            ])

                        </ul>
                    </li>

                    {{-- header --}}
                    @include('backend.includes.navbar.menu_buttons', [
                        'menuItems' => [
                            [
                                'pageSlug' => 'menus',
                                'routeName' => 'backend.menu.header_list',
                                'iconClass' => 'fa fa-edit',
                                'label' => ' Header',
                            ],
                        ],
                    ])

                    {{-- Footer management --}}
                    <li
                        class="nav-item @if (request()->routeIs('backend.footer_zone.*')) menu-open @endif
                        @if (!check_access_by_route_name('backend.footer_zone.title')) d-none @endif">
                        <a href="#" class="nav-link align-items-center m-1">
                            <i class="fa fa-edit"></i>
                            <p>
                                Footer
                                <i class="nav-arrow fa fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" role="navigation" aria-label="Navigation 15"
                            style="box-sizing: border-box; @if (request()->routeIs('backend.footer_zone.*') || (request()->routeIs('backend.single_page.sp.show') &&
                            ($currentPageSlug == 'footer-copyright-section'))) display: block; @else display: none; @endif ">

                            @include('backend.includes.navbar.menu_buttons', [
                                'menuItems' => [
                                    [
                                        'pageSlug' => 'footer-zone-title',
                                        'routeName' => 'backend.footer_zone.title.footer_zone_title_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Footer Zone Title',
                                    ],
                                    [
                                        'pageSlug' => 'footer-zone-item',
                                        'routeName' => 'backend.footer_zone.item.footer_zone_item_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Footer Zone Item',
                                    ],
                                    [
                                        'pageSlug' => 'footer-copyright-section',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'footer-copyright-section',
                                        'permission' => 'footer_copyright_section',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Footer Copyright Section',
                                    ],
                                ],
                            ])

                        </ul>
                    </li>

                    {{-- Home Page Management --}}
                    @php
                        $homePageParams = [
                            'home-page-hero-section',
                            'home-page-inspire-section',
                            'home-page-adventure-section',
                            'call-to-action-footer',
                            'home-page-expression-section',
                        ];

                        $isHomePageRoute =
                            request()->routeIs('backend.single_page.sp.show') &&
                            in_array($currentPageSlug, $homePageParams);
                        $isHomePageMenuOpen =
                            $isHomePageRoute ||
                            request()->routeIs('backend.hero_section.*') ||
                            request()->routeIs('backend.promise_section.*') ||
                            request()->routeIs('backend.partner.*') ||
                            request()->routeIs('backend.why_choose_us.*');
                            //  || request()->routeIs('backend.faq.*');
                    @endphp
                    <li
                        class="nav-item @if ($isHomePageMenuOpen) menu-open @endif
                        @if (!check_access_by_route_name('backend.single_page.sp.show')) d-none @endif">
                        <a href="#" class="nav-link align-items-center m-1">
                            {{-- <i class="fa fa-users"></i> --}}
                            <i class="fa fa-edit"></i>
                            <p>
                                Home Page
                                <i class="nav-arrow fa fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" role="navigation" aria-label="Navigation 15"
                            style="box-sizing: border-box; @if ($isHomePageMenuOpen) display: block; @else display: none; @endif ">

                            @include('backend.includes.navbar.menu_buttons', [
                                'menuItems' => [
                                    [
                                        'pageSlug' => 'home-page-hero-section',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'home-page-hero-section',
                                        'permission' => 'home_page_hero_section',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Hero Section',
                                    ],
                                    [
                                        'pageSlug' => 'hero-section-icons',
                                        'routeName' => 'backend.hero_section.hero_section_icons_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Hero Section Icons',
                                    ],
                                    [
                                        'pageSlug' => 'promise-section',
                                        'routeName' => 'backend.promise_section.promise_section_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Promise Section',
                                    ],
                                    [
                                        'pageSlug' => 'home-page-inspire-section',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'home-page-inspire-section',
                                        'permission' => 'home_page_inspire_section',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Inspire Section',
                                    ],
                                    [
                                        'pageSlug' => 'home-page-expression-section',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'home-page-expression-section',
                                        'permission' => 'home_page_expression_section',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Expression Section',
                                    ],
                                    [
                                        'pageSlug' => 'partner-companies',
                                        'routeName' => 'backend.partner.partner_compnay_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Partner Section',
                                    ],
                                    [
                                        'pageSlug' => 'home-page-adventure-section',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'home-page-adventure-section',
                                        'permission' => 'home_page_adventure_section',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Adventure Section',
                                    ],
                                    [
                                        'pageSlug' => 'why-choose-us',
                                        'routeName' => 'backend.why_choose_us.why_choose_us_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Why Choose Us Section',
                                    ],
                                    [
                                        'pageSlug' => 'call-to-action-footer',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'call-to-action-footer',
                                        'permission' => 'footer_cta_section',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Footer CTA Section',
                                    ],

                                ],
                            ])

                        </ul>
                    </li>

                    {{-- Generic Pages  --}}
                    @php
                        $privacyPolicyParams = [
                            'policy-privacy',
                            'policy-refund',
                            'policy-children',
                            'policy-deletion',
                            'order-track',
                            'about-us',
                            'contact-us',

                        ];
                        $currentPageSlug = request()->route() ? request()->route()->parameter('page_slug') : null;
                        $isprivacyPolicyRoute =
                            request()->routeIs('backend.single_page.sp.show') &&
                            in_array($currentPageSlug, $privacyPolicyParams);
                    @endphp
                    <li
                        class="nav-item @if ($isprivacyPolicyRoute || request()->routeIs('backend.faq.*')) menu-open @endif
                        @if (!check_access_by_route_name('backend.single_page.sp.show')) d-none @endif">
                        <a href="#" class="nav-link align-items-center m-1">
                            <i class="fa fa-edit"></i>
                            <p>
                                Generic Pages
                                <i class="nav-arrow fa fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" role="navigation" aria-label="Navigation 15"
                            style="box-sizing: border-box; @if ($isprivacyPolicyRoute || request()->routeIs('backend.faq.*')) display: block; @else display: none; @endif ">

                            @include('backend.includes.navbar.menu_buttons', [
                                'menuItems' => [
                                    [
                                        'pageSlug' => 'order-track',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'order-track',
                                        'permission' => 'track_your_order',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Track Your Order',
                                    ],
                                    [
                                        'pageSlug' => 'about-us',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'about-us',
                                        'permission' => 'about_us',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'About Us',
                                    ],
                                    [
                                        'pageSlug' => 'contact-us',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'contact-us',
                                        'permission' => 'contact_us',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Contact Us',
                                    ],
                                    [
                                        'pageSlug' => 'faq',
                                        'routeName' => 'backend.faq.faq_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'FAQ Page',
                                    ],
                                    [
                                        'pageSlug' => 'policy-privacy',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'policy-privacy',
                                        'permission' => 'policy_privacy',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Privacy & Data Policy',
                                    ],
                                    [
                                        'pageSlug' => 'policy-refund',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'policy-refund',
                                        'permission' => 'policy_refund',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Refund & Return Policy',
                                    ],
                                    [
                                        'pageSlug' => 'policy-children',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'policy-children',
                                        'permission' => 'policy_children',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Children’s Data & Safety Policy',
                                    ],
                                    [
                                        'pageSlug' => 'policy-deletion',
                                        'routeName' => 'backend.single_page.sp.show',
                                        'params' => 'policy-deletion',
                                        'permission' => 'policy_deletion',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Data Deletion Policy',
                                    ],

                                ],
                            ])

                        </ul>
                    </li>


                    {{-- Email templates --}}
                    @include('backend.includes.navbar.menu_buttons', [
                        'menuItems' => [
                            [
                                'pageSlug' => 'email_templates',
                                'routeName' => 'backend.email.templates.email_templates_list',
                                'iconClass' => 'fa fa-envelope',
                                'label' => 'Email Templates',
                            ],
                        ],
                    ])

                    {{-- Books --}}
                    <li
                        class="nav-item @if (request()->routeIs('backend.book.*')) menu-open @endif">
                        <a href="#" class="nav-link align-items-center m-1">
                            <i class="fa fa-book"></i>
                            <p>
                                Books Management
                                <i class="nav-arrow fa fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" role="navigation" aria-label="Navigation 15"
                            style="box-sizing: border-box; @if (request()->routeIs('backend.book.*')) display: block; @else display: none; @endif ">

                            @include('backend.includes.navbar.menu_buttons', [
                                'menuItems' => [
                                    [
                                        'pageSlug' => 'books',
                                        'routeName' => 'backend.book.book_list',
                                        'iconClass' => 'fa fa-circle',
                                        'label' => 'Books',
                                    ],
                                ],
                            ])

                            {{-- Book Setup --}}
                            <li
                                class="nav-item @if (request()->routeIs('backend.book.setup.*')) menu-open @endif">
                                <a href="#" class="nav-link align-items-center m-1">
                                    <i class="fa fa-cog"></i>
                                    <p>
                                        Setup
                                        <i class="nav-arrow fa fa-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview" role="navigation" aria-label="Navigation 15"
                                    style="box-sizing: border-box; @if (request()->routeIs('backend.book.setup.*')) display: block; @else display: none; @endif ">

                                    @include('backend.includes.navbar.menu_buttons', [
                                        'menuItems' => [
                                            [
                                                'pageSlug' => 'book_setup_skin_color',
                                                'routeName' => 'backend.book.setup.skin-color.skin_color_list',
                                                'iconClass' => 'fa fa-circle',
                                                'label' => 'Skin Color',
                                            ],
                                            [
                                                'pageSlug' => 'book_setup_gender',
                                                'routeName' => 'backend.book.setup.gender.gender_list',
                                                'iconClass' => 'fa fa-circle',
                                                'label' => 'Gender',
                                            ],
                                            [
                                                'pageSlug' => 'book_setup_book_format',
                                                'routeName' => 'backend.book.setup.book-format.book_format_list',
                                                'iconClass' => 'fa fa-circle',
                                                'label' => 'Book Format',
                                            ],
                                            [
                                                'pageSlug' => 'book_setup_book_size',
                                                'routeName' => 'backend.book.setup.book-size.book_size_list',
                                                'iconClass' => 'fa fa-circle',
                                                'label' => 'Book Size',
                                            ],
                                            [
                                                'pageSlug' => 'book_setup_story_type',
                                                'routeName' => 'backend.book.setup.story-type.story_type_list',
                                                'iconClass' => 'fa fa-circle',
                                                'label' => 'Story Type',
                                            ],
                                            [
                                                'pageSlug' => 'book_setup_book_tag',
                                                'routeName' => 'backend.book.setup.book-tag.book_tag_list',
                                                'iconClass' => 'fa fa-circle',
                                                'label' => 'Book Tags',
                                            ],
                                            [
                                                'pageSlug' => 'book_setup_age_range',
                                                'routeName' => 'backend.book.setup.age-range.age_range_list',
                                                'iconClass' => 'fa fa-circle',
                                                'label' => 'Age Range',
                                            ],
                                        ],
                                    ])

                                </ul>
                            </li>

                        </ul>
                    </li>

                    {{-- Language Management --}}
                    <li
                        class="nav-item @if (request()->routeIs('backend.language_management.*')) menu-open @endif">
                        <a href="#" class="nav-link align-items-center m-1">
                            <i class="fa fa-language"></i>
                            <p>
                                Language Management
                                <i class="nav-arrow fa fa-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" role="navigation" aria-label="Navigation 16"
                            style="box-sizing: border-box; @if (request()->routeIs('backend.language_management.*')) display: block; @else display: none; @endif ">

                            {{-- Language Setup --}}
                            <li
                                class="nav-item @if (request()->routeIs('backend.language_management.setup.*')) menu-open @endif">
                                <a href="#" class="nav-link align-items-center m-1">
                                    <i class="fa fa-cog"></i>
                                    <p>
                                        Setup
                                        <i class="nav-arrow fa fa-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview" role="navigation" aria-label="Navigation 17"
                                    style="box-sizing: border-box; @if (request()->routeIs('backend.language_management.setup.*')) display: block; @else display: none; @endif ">

                                    @include('backend.includes.navbar.menu_buttons', [
                                        'menuItems' => [
                                            [
                                                'pageSlug' => 'language_setup_language',
                                                'routeName' => 'backend.language_management.setup.language.language_list',
                                                'iconClass' => 'fa fa-circle',
                                                'label' => 'Languages',
                                            ],
                                        ],
                                    ])

                                </ul>
                            </li>

                        </ul>
                    </li>

                    {{-- Application settings --}}
                    @include('backend.includes.navbar.menu_buttons', [
                        'menuItems' => [
                            [
                                'pageSlug' => 'app_settings',
                                'routeName' => 'backend.app.settings.index.application_settings',
                                'iconClass' => 'fa fa-cog',
                                'label' => 'Application Settings',
                            ],
                        ],
                    ])
                </ul>
            </nav>
        </div>
    </div>
</aside>
