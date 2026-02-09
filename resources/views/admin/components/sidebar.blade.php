<aside class="app-sidebar sticky" id="sidebar" aria-label="Main navigation">
    <div class="app-sidebar-header">
        <!-- Logo for light mode -->
        <a href="{{ url('/') }}" class="desktop-logo" aria-label="Home">
            <img src="{{ asset('default_images/logo.png') }}" alt="Company Logo" loading="lazy">
        </a>

        <!-- Logo for dark mode -->
        <a href="{{ url('/') }}" class="desktop-dark" aria-label="Home">
            <img src="{{ asset('default_images/logo.png') }}" alt="Company Logo Dark Mode" loading="lazy">
        </a>
    </div>

    <div class="app-sidebar-wrapper" id="sidebar-scroll">
        <nav class="app-sidebar-menu-wrapper nav flex-column" aria-label="Sidebar menu">
            <div class="sidebar-left" id="sidebar-left"></div>

            <ul class="app-sidebar-main-menu" role="menubar">
                <!-- Main Category -->
                <li class="sidebar-menu-category" role="presentation">
                    <span class="category-name">Main</span>
                </li>

                <!-- Dashboard -->
                @can('dashboard.view')
                    <li class="slide" role="none">
                        <a href="{{ route('admin.dashboard') }}"
                            class="sidebar-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            role="menuitem" aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : false }}">
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M13 21V11H21V21H13ZM3 13V3H11V13H3ZM9 11V5H5V11H9ZM3 21V15H11V21H3ZM5 19H9V17H5V19ZM15 19H19V13H15V19ZM13 3H21V9H13V3ZM15 5V7H19V5H15Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Dashboard</span>
                        </a>
                    </li>
                @endcan

                <!-- User Management -->
                @canany(['users.view.any', 'staff.view.any', 'roles.view.any'])
                    <li class="slide has-sub {{ request()->routeIs('users.*', 'staff.*', 'roles.*') ? 'open' : '' }}"
                        role="none">
                        <a href="javascript:void(0)"
                            class="sidebar-menu-item {{ request()->routeIs('users.*', 'staff.*', 'roles.*') ? 'active' : '' }}"
                            role="menuitem" aria-haspopup="true"
                            aria-expanded="{{ request()->routeIs('users.*', 'staff.*', 'roles.*') ? 'true' : 'false' }}"
                            data-submenu-toggle>
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M12 11C14.7614 11 17 13.2386 17 16V22H15V16C15 14.4023 13.7511 13.0963 12.1763 13.0051L12 13C10.4023 13 9.09634 14.2489 9.00509 15.8237L9 16V22H7V16C7 13.2386 9.23858 11 12 11ZM5.5 14C5.77885 14 6.05009 14.0326 6.3101 14.0942C6.14202 14.594 6.03873 15.122 6.00896 15.6693L6 16L6.0007 16.0856C5.88757 16.0456 5.76821 16.0187 5.64446 16.0069L5.5 16C4.7203 16 4.07955 16.5949 4.00687 17.3555L4 17.5V21H2V17.5C2 15.567 3.567 14 5.5 14ZM18.5 14C20.433 14 22 15.567 22 17.5V21H20V17.5C20 16.7203 19.4051 16.0796 18.6445 16.0069L18.5 16C18.3248 16 18.1566 16.03 18.0003 16.0852C18.0001 16.0568 18 16.0284 18 16C18 15.3343 17.8916 14.694 17.6915 14.0956C17.9499 14.0326 18.2211 14 18.5 14ZM5.5 8C6.88071 8 8 9.11929 8 10.5C8 11.8807 6.88071 13 5.5 13C4.11929 13 3 11.8807 3 10.5C3 9.11929 4.11929 8 5.5 8ZM18.5 8C19.8807 8 21 9.11929 21 10.5C21 11.8807 19.8807 13 18.5 13C17.1193 13 16 11.8807 16 10.5C16 9.11929 17.1193 8 18.5 8ZM5.5 10C5.22386 10 5 10.2239 5 10.5C5 10.7761 5.22386 11 5.5 11C5.77614 11 6 10.7761 6 10.5C6 10.2239 5.77614 10 5.5 10ZM18.5 10C18.2239 10 18 10.2239 18 10.5C18 10.7761 18.2239 11 18.5 11C18.7761 11 19 10.7761 19 10.5C19 10.2239 18.7761 10 18.5 10ZM12 2C14.2091 2 16 3.79086 16 6C16 8.20914 14.2091 10 12 10C9.79086 10 8 8.20914 8 6C8 3.79086 9.79086 2 12 2ZM12 4C10.8954 4 10 4.89543 10 6C10 7.10457 10.8954 8 12 8C13.1046 8 14 7.10457 14 6C14 4.89543 13.1046 4 12 4Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">User Management</span>
                            <i class="ri-arrow-down-s-fill side-menu-angle" aria-hidden="true"></i>
                        </a>

                        <ul class="sidebar-menu child1" role="menu">
                            @can('users.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('users.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                        role="menuitem" aria-current="{{ request()->routeIs('users.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Customer Accounts</span>
                                    </a>
                                </li>
                            @endcan

                            @role('admin')
                                <li class="slide" role="none">
                                    <a href="{{ route('staff.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('staff.*') ? 'active' : '' }}"
                                        role="menuitem" aria-current="{{ request()->routeIs('staff.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Staff Members</span>
                                    </a>
                                </li>

                                <li class="slide" role="none">
                                    <a href="{{ route('roles.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                                        role="menuitem" aria-current="{{ request()->routeIs('roles.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Roles & Permissions</span>
                                    </a>
                                </li>
                            @endrole
                        </ul>
                    </li>
                @endcanany

                <!-- Products Management -->
                @canany(['categories.view.any', 'subcategories.view.any', 'products.view.any', 'attributes.view.any'])
                    <li class="slide has-sub {{ request()->routeIs('categories.*', 'subcategories.*', 'products.*', 'attributes.*') ? 'open' : '' }}"
                        role="none">
                        <a href="javascript:void(0)"
                            class="sidebar-menu-item {{ request()->routeIs('categories.*', 'subcategories.*', 'products.*', 'attributes.*') ? 'active' : '' }}"
                            role="menuitem" aria-haspopup="true"
                            aria-expanded="{{ request()->routeIs('categories.*', 'subcategories.*', 'products.*', 'attributes.*') ? 'true' : 'false' }}"
                            data-submenu-toggle>
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M21 13.2422V20H22V22H2V20H3V13.2422C1.79401 12.435 1 11.0602 1 9.5C1 8.67286 1.22443 7.87621 1.63322 7.19746L4.3453 2.5C4.52393 2.1906 4.85406 2 5.21132 2H18.7887C19.1459 2 19.4761 2.1906 19.6547 2.5L22.3575 7.18172C22.7756 7.87621 23 8.67286 23 9.5C23 11.0602 22.206 12.435 21 13.2422ZM19 13.9725C18.8358 13.9907 18.669 14 18.5 14C17.2409 14 16.0789 13.478 15.25 12.6132C14.4211 13.478 13.2591 14 12 14C10.7409 14 9.5789 13.478 8.75 12.6132C7.9211 13.478 6.75911 14 5.5 14C5.331 14 5.16417 13.9907 5 13.9725V20H19V13.9725ZM5.78865 4L3.35598 8.21321C3.12409 8.59843 3 9.0389 3 9.5C3 10.8807 4.11929 12 5.5 12C6.53096 12 7.44467 11.3703 7.82179 10.4295C8.1574 9.59223 9.3426 9.59223 9.67821 10.4295C10.0553 11.3703 10.969 12 12 12C13.031 12 13.9447 11.3703 14.3218 10.4295C14.6574 9.59223 15.8426 9.59223 16.1782 10.4295C16.5553 11.3703 17.469 12 18.5 12C19.8807 12 21 10.8807 21 9.5C21 9.0389 20.8759 8.59843 20.6347 8.19746L18.2113 4H5.78865Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Catalog Management</span>
                            <i class="ri-arrow-down-s-fill side-menu-angle" aria-hidden="true"></i>
                        </a>

                        <ul class="sidebar-menu child1" role="menu">
                            @can('attributes.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('attributes.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('attributes.*') ? 'active' : '' }}"
                                        role="menuitem"
                                        aria-current="{{ request()->routeIs('attributes.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Attributes</span>
                                    </a>
                                </li>
                            @endcan

                            @can('categories.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('categories.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                                        role="menuitem"
                                        aria-current="{{ request()->routeIs('categories.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Categories</span>
                                    </a>
                                </li>
                            @endcan

                            @can('subcategories.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('subcategories.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('subcategories.*') ? 'active' : '' }}"
                                        role="menuitem"
                                        aria-current="{{ request()->routeIs('subcategories.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Sub Categories</span>
                                    </a>
                                </li>
                            @endcan

                            @can('products.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('products.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('products.*') ? 'active' : '' }}"
                                        role="menuitem"
                                        aria-current="{{ request()->routeIs('products.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Products</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Orders -->
                @can('orders.view.any')
                    <li class="slide" role="none">
                        <a href="{{ route('orders.index') }}"
                            class="sidebar-menu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                            role="menuitem" aria-current="{{ request()->routeIs('orders.*') ? 'page' : false }}">
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M6.00488 9H19.9433L20.4433 7H8.00488V5H21.7241C22.2764 5 22.7241 5.44772 22.7241 6C22.7241 6.08176 22.7141 6.16322 22.6942 6.24254L20.1942 16.2425C20.083 16.6877 19.683 17 19.2241 17H5.00488C4.4526 17 4.00488 16.5523 4.00488 16V4H2.00488V2H5.00488C5.55717 2 6.00488 2.44772 6.00488 3V9ZM6.00488 23C4.90031 23 4.00488 22.1046 4.00488 21C4.00488 19.8954 4.90031 19 6.00488 19C7.10945 19 8.00488 19.8954 8.00488 21C8.00488 22.1046 7.10945 23 6.00488 23ZM18.0049 23C16.9003 23 16.0049 22.1046 16.0049 21C16.0049 19.8954 16.9003 19 18.0049 19C19.1095 19 20.0049 19.8954 20.0049 21C20.0049 22.1046 19.1095 23 18.0049 23Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Orders Management</span>
                        </a>
                    </li>
                @endcan

                <!-- Banners -->
                @can('banners.view.any')
                    <li class="slide" role="none">
                        <a href="{{ route('banners.index') }}"
                            class="sidebar-menu-item {{ request()->routeIs('banners.*') ? 'active' : '' }}"
                            role="menuitem" aria-current="{{ request()->routeIs('banners.*') ? 'page' : false }}">
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M2 3.9934C2 3.44476 2.45531 3 2.9918 3H21.0082C21.556 3 22 3.44495 22 3.9934V20.0066C22 20.5552 21.5447 21 21.0082 21H2.9918C2.44405 21 2 20.5551 2 20.0066V3.9934ZM20 15V5H4V19L14 9L20 15ZM20 17.8284L14 11.8284L6.82843 19H20V17.8284ZM8 11C6.89543 11 6 10.1046 6 9C6 7.89543 6.89543 7 8 7C9.10457 7 10 7.89543 10 9C10 10.1046 9.10457 11 8 11Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Banner & Slider</span>
                        </a>
                    </li>
                @endcan

                <!-- Transactions -->
                @can('transactions.view.any')
                    <li class="slide" role="none">
                        <a href="{{ route('transactions.index') }}"
                            class="sidebar-menu-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                            role="menuitem" aria-current="{{ request()->routeIs('transactions.*') ? 'page' : false }}">
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M7 7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V7H20.0066C20.5552 7 21 7.44495 21 7.9934V21.0066C21 21.5552 20.5551 22 20.0066 22H3.9934C3.44476 22 3 21.5551 3 21.0066V7.9934C3 7.44476 3.44495 7 3.9934 7H7ZM5 9V20H19V9H5ZM9 4V7H15V4H9ZM11 12H13V17H11V12Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Transactions</span>
                        </a>
                    </li>
                @endcan

                <!-- Coupons -->
                @can('coupons.view.any')
                    <li class="slide" role="none">
                        <a href="{{ route('coupons.index') }}"
                            class="sidebar-menu-item {{ request()->routeIs('coupons.*') ? 'active' : '' }}"
                            role="menuitem" aria-current="{{ request()->routeIs('coupons.*') ? 'page' : false }}">
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M2.00488 9.5V4C2.00488 3.44772 2.4526 3 3.00488 3H21.0049C21.5572 3 22.0049 3.44772 22.0049 4V9.5C20.6242 9.5 19.5049 10.6193 19.5049 12C19.5049 13.3807 20.6242 14.5 22.0049 14.5V20C22.0049 20.5523 21.5572 21 21.0049 21H3.00488C2.4526 21 2.00488 20.5523 2.00488 20V14.5C3.38559 14.5 4.50488 13.3807 4.50488 12C4.50488 10.6193 3.38559 9.5 2.00488 9.5ZM14.0049 5H4.00488V7.96783C5.46975 8.54439 6.50488 9.99315 6.50488 11.7C6.50488 13.4069 5.46975 14.8556 4.00488 15.4322V19H14.0049V5ZM16.0049 5V19H20.0049V15.4322C18.54 14.8556 17.5049 13.4069 17.5049 11.7C17.5049 9.99315 18.54 8.54439 20.0049 7.96783V5H16.0049Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Discount & Coupons</span>
                        </a>
                    </li>
                @endcan

                <!-- Rating & Review -->
                @can('reviews.view.any')
                    <li class="slide" role="none">
                        <a href="{{ route('reviews.index') }}"
                            class="sidebar-menu-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}"
                            role="menuitem" aria-current="{{ request()->routeIs('reviews.*') ? 'page' : false }}">
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M12.0006 18.26L4.94715 22.2082L6.52248 14.2799L0.587891 8.7918L8.61493 7.84006L12.0006 0.5L15.3862 7.84006L23.4132 8.7918L17.4787 14.2799L19.054 22.2082L12.0006 18.26Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Rating & Review</span>
                        </a>
                    </li>
                @endcan

                <!-- Content & Settings Category -->
                <li class="sidebar-menu-category" role="presentation">
                    <span class="category-name">Other</span>
                </li>

                <!-- Content & Blogs -->
                @canany(['contents.view.any', 'blogs.view.any'])
                    <li class="slide has-sub {{ request()->routeIs('contents.*', 'blogs.*') ? 'open' : '' }}"
                        role="none">
                        <a href="javascript:void(0)"
                            class="sidebar-menu-item {{ request()->routeIs('contents.*', 'blogs.*') ? 'active' : '' }}"
                            role="menuitem" aria-haspopup="true"
                            aria-expanded="{{ request()->routeIs('contents.*', 'blogs.*') ? 'true' : 'false' }}"
                            data-submenu-toggle>
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M20 22H4C3.44772 22 3 21.5523 3 21V3C3 2.44772 3.44772 2 4 2H20C20.5523 2 21 2.44772 21 3V21C21 21.5523 20.5523 22 20 22ZM19 20V4H5V20H19ZM7 6H11V10H7V6ZM7 12H17V14H7V12ZM7 16H17V18H7V16ZM13 7H17V9H13V7Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Content & Blogs</span>
                            <i class="ri-arrow-down-s-fill side-menu-angle" aria-hidden="true"></i>
                        </a>

                        <ul class="sidebar-menu child1" role="menu">
                            @can('contents.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('contents.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('contents.*') ? 'active' : '' }}"
                                        role="menuitem"
                                        aria-current="{{ request()->routeIs('contents.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Content Pages</span>
                                    </a>
                                </li>
                            @endcan

                            @can('blogs.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('blogs.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('blogs.*') ? 'active' : '' }}"
                                        role="menuitem" aria-current="{{ request()->routeIs('blogs.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Blog Posts</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Settings -->
                @canany(['metals.view.any', 'newsletters.view.any', 'settings.view.any'])
                    <li class="slide has-sub {{ request()->routeIs('metals.*', 'newsletters.*', 'settings.*') ? 'open' : '' }}"
                        role="none">
                        <a href="javascript:void(0)"
                            class="sidebar-menu-item {{ request()->routeIs('metals.*', 'newsletters.*', 'settings.*') ? 'active' : '' }}"
                            role="menuitem" aria-haspopup="true"
                            aria-expanded="{{ request()->routeIs('metals.*', 'newsletters.*', 'settings.*') ? 'true' : 'false' }}"
                            data-submenu-toggle>
                            <div class="side-menu-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="24" height="24" focusable="false">
                                    <path
                                        d="M8.68637 4.00008L11.293 1.39348C11.6835 1.00295 12.3167 1.00295 12.7072 1.39348L15.3138 4.00008H19.0001C19.5524 4.00008 20.0001 4.4478 20.0001 5.00008V8.68637L22.6067 11.293C22.9972 11.6835 22.9972 12.3167 22.6067 12.7072L20.0001 15.3138V19.0001C20.0001 19.5524 19.5524 20.0001 19.0001 20.0001H15.3138L12.7072 22.6067C12.3167 22.9972 11.6835 22.9972 11.293 22.6067L8.68637 20.0001H5.00008C4.4478 20.0001 4.00008 19.5524 4.00008 19.0001V15.3138L1.39348 12.7072C1.00295 12.3167 1.00295 11.6835 1.39348 11.293L4.00008 8.68637V5.00008C4.00008 4.4478 4.4478 4.00008 5.00008 4.00008H8.68637ZM6.00008 6.00008V9.5148L3.5148 12.0001L6.00008 14.4854V18.0001H9.5148L12.0001 20.4854L14.4854 18.0001H18.0001V14.4854L20.4854 12.0001L18.0001 9.5148V6.00008H14.4854L12.0001 3.5148L9.5148 6.00008H6.00008ZM12.0001 16.0001C9.79094 16.0001 8.00008 14.2092 8.00008 12.0001C8.00008 9.79094 9.79094 8.00008 12.0001 8.00008C14.2092 8.00008 16.0001 9.79094 16.0001 12.0001C16.0001 14.2092 14.2092 16.0001 12.0001 16.0001ZM12.0001 14.0001C13.1047 14.0001 14.0001 13.1047 14.0001 12.0001C14.0001 10.8955 13.1047 10.0001 12.0001 10.0001C10.8955 10.0001 10.0001 10.8955 10.0001 12.0001C10.0001 13.1047 10.8955 14.0001 12.0001 14.0001Z" />
                                </svg>
                            </div>
                            <span class="sidebar-menu-label">Settings</span>
                            <i class="ri-arrow-down-s-fill side-menu-angle" aria-hidden="true"></i>
                        </a>

                        <ul class="sidebar-menu child1" role="menu">
                            @can('metals.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('metals.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('metals.*') ? 'active' : '' }}"
                                        role="menuitem" aria-current="{{ request()->routeIs('metals.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Price Settings</span>
                                    </a>
                                </li>
                            @endcan

                            @can('newsletters.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('newsletters.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('newsletters.*') ? 'active' : '' }}"
                                        role="menuitem"
                                        aria-current="{{ request()->routeIs('newsletters.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>Newsletter Subscribers</span>
                                    </a>
                                </li>
                            @endcan

                            @can('settings.view.any')
                                <li class="slide" role="none">
                                    <a href="{{ route('settings.index') }}"
                                        class="sidebar-menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                                        role="menuitem"
                                        aria-current="{{ request()->routeIs('settings.*') ? 'page' : false }}">
                                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                        <span>General Settings</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            </ul>

            <div class="sidebar-right" id="sidebar-right"></div>
        </nav>
    </div>
</aside>

<div class="app-offcanvas-overlay" aria-hidden="true"></div>
