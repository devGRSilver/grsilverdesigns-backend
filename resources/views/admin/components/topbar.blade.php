 <div class="app-header-area">
     <header class="app-header" id="header">
         <div class="app-header-inner">
             <div class="app-header-left">
                 <div class="d-flex align-center gap-15">
                     <div class="app-header-element">
                         <a class="sidebar-toggle-bar" id="sidebarToggle" href="javascript:void(0);">
                             <div class="sidebar-menu-bar">
                                 <span></span>
                                 <span></span>
                                 <span></span>
                             </div>
                         </a>
                     </div>
                     <div class="app-header-ls-logo">
                         <!-- large screen logo -->
                         <a class="app-header-ls-dark-logo" href="#">
                             <img src="{{ URL::asset('default_images/logo.png') }}" alt="image">
                         </a>
                         <a class="app-header-ls-light-logo" href="#">
                             <img src="{{ URL::asset('default_images/logo.png') }}" alt="image">
                         </a>
                     </div>
                     <div class="app-header-mobile-logo">
                         <a class="app-header-dark-logo" href="#">
                             <img src="{{ URL::asset('default_images/logo.png') }}" alt="image">
                         </a>
                         <a class="app-header-light-logo" href="#">
                             <img src="{{ URL::asset('default_images/logo.png') }}" alt="image">
                         </a>
                     </div>
                 </div>
                 <div class="app-header-search d-none d-lg-block">
                     <div class="search-wrapper" id="searchWrapper">
                         <form id="globalSearchForm" class="search-form">
                             @csrf
                             <div class="search-input-wrapper">
                                 <i class="fas fa-search search-icon"></i>
                                 <input type="text" id="globalSearchInput" class="search-input"
                                     placeholder="Search users, orders..." autocomplete="off">
                                 <button type="button" class="clear-btn" id="clearBtn">
                                     <i class="fas fa-times"></i>
                                 </button>
                             </div>
                         </form>

                         <div class="results-container" id="resultsContainer">
                             <div id="searchContent"></div>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="app-header-right">
                 <div class="app-header-search-modal">
                     <button type="button" class="app-header-circle" data-bs-toggle="modal"
                         data-bs-target="#searchModal">
                         <i class="ri-search-line"></i>
                     </button>
                 </div>





                 <!-- Notification Bell -->
                 <div class="app-header-utc-time text-muted">
                     <b> {{ \Carbon\Carbon::now('UTC')->format('D, d M Y') }}</b>
                 </div>

                 <div class="app-header-notification">
                     <div class="dropdown">
                         <a class="dropdown-toggle" href="javascript:void(0);" role="button" data-bs-toggle="dropdown"
                             aria-expanded="false">
                             <span class="app-header-circle position-relative">
                                 <i class="ri-notification-line"></i>
                                 <!-- Unread badge -->
                                 <span id="notificationBadge"
                                     class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                             </span>
                         </a>
                         <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 350px;">
                             <li class="dropdown-menu-header p-3 d-flex justify-content-between align-items-center">
                                 <span>Notifications</span>
                                 <button id="markAllRead" class="btn btn-sm btn-outline-primary">Mark all
                                     read</button>
                             </li>
                             <li class="dropdown-notifications-list card-scrollbar"
                                 style="max-height: 400px; overflow-y: auto;">
                                 <ul id="notificationsList" class="list-unstyled mb-0">
                                     <!-- Notifications will be dynamically appended here via JS -->
                                 </ul>
                             </li>
                             <li class="dropdown-notifications-btn p-2 text-center">
                                 <a href="#" class="btn btn-primary w-100">View All
                                     Notifications</a>
                             </li>
                         </ul>
                     </div>
                 </div>




                 <div class="app-header-user">
                     <div class="dropdown">
                         <a class="dropdown-toggle" href="javascript:void(0);" role="button" data-bs-toggle="dropdown"
                             aria-expanded="false">
                             <div class="author">
                                 <div class="author-thumb">
                                     <img src="{{ URL::asset('default_images/no_user.png') }}" alt="user">
                                 </div>
                                 <h6 class="author-name lh-1"> {{ Auth::user()->name ?? '' }}</h6>
                             </div>
                         </a>
                         <ul class="dropdown-menu">
                             <li class="bd-user-info-list"><a href="{{ route('settings.index') }}"><i
                                         class="ri-user-line"></i>Profile & Setting </a>
                             </li>

                             <li class="bd-user-info-list">
                                 <a href="javascript:void(0)" data-url="{{ route('admin.logout') }}" data-method="POST"
                                     class="click_ajax_button">
                                     <i class="ri-logout-circle-line"></i> Logout
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </div>
             </div>
         </div>
     </header>
     <div class="body__overlay"></div>
 </div>
