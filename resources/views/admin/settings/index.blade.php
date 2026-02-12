@extends('layouts.admin')
@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Users' }}</h1>

                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Settings</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="col-xxl-12 col-xl-12 order-xxl-1 order-2">
                                    <div class="card">
                                        <div class="tab-style-three">
                                            <ul class="nav nav-pills gap-10 b-bottom2px b-color-primary mobile-nav"
                                                id="pills-tab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="pills-activity-tab"
                                                        data-bs-toggle="pill" data-bs-target="#pills-activity"
                                                        type="button" role="tab" aria-controls="pills-activity"
                                                        aria-selected="true">Profile</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-notes-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-notes" type="button" role="tab"
                                                        aria-controls="pills-notes" aria-selected="false">Website
                                                        Setting</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-calls-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-calls" type="button" role="tab"
                                                        aria-controls="pills-calls" aria-selected="false">tab3</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-email-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-email" type="button" role="tab"
                                                        aria-controls="pills-email" aria-selected="true">tab4</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-task-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-task" type="button" role="tab"
                                                        aria-controls="pills-task" aria-selected="false">tab5</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-files-tab" data-bs-toggle="pill"
                                                        data-bs-target="#pills-files" type="button" role="tab"
                                                        aria-controls="pills-files" aria-selected="false">tab6</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-activity" role="tabpanel"
                                                aria-labelledby="pills-activity-tab" tabindex="0">
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <div class="customer-nav mb-25 mobile-nav">
                                                            <ul class="d-flex-items gap-10">
                                                                <li class="active"><a class="btn btn-primary"
                                                                        href="#">User
                                                                        Info</a>
                                                                </li>
                                                                <li class=""><a class="btn btn-light modal_open"
                                                                        href="{{ route('users.password.edit', encrypt($data->id)) }}">
                                                                        Change Password
                                                                    </a></li>
                                                                <li class=""><a class="btn btn-light modal_open"
                                                                        href="{{ route('users.edit', encrypt($data->id)) }}">Edit
                                                                    </a>
                                                                </li>






                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="card">
                                                            <div class="card-header justify-between">
                                                                <h4 class="">Personal Information</h4>
                                                                <div class="card-dropdown">
                                                                    <div class="dropdown">
                                                                        <a class="card-dropdown-icon"
                                                                            href="javascript:void(0);" role="button"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ri-more-2-fill"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item modal_open"
                                                                                href="{{ route('users.edit', encrypt($data->id)) }}">Edit</a>
                                                                            <a class="dropdown-item modal_open"
                                                                                href="{{ route('users.password.edit', encrypt($data->id)) }}">Change
                                                                                Password </a>


                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="card-body pt-15">
                                                                <div class="text-center mb-10">
                                                                    <div class="avatar avatar-big radius-100">
                                                                        <img class="radius-100"
                                                                            src="{{ $data->profile_picture }}"
                                                                            alt="image not found">
                                                                    </div>
                                                                </div>
                                                                <div class="profile-info text-center mb-15">
                                                                    <h3 class="mb-5">{{ $data->name }}</h3>
                                                                    <h6 class="text-body mb-10">User ID: #
                                                                        {{ $data->id }}</h6>
                                                                    <div class="d-flex-center gap-15">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn-icon btn-warning-light fs-16">
                                                                            <i class="ri-twitter-x-line"></i>
                                                                        </a><a href="javascript:void(0);"
                                                                            class="btn-icon btn-success-light fs-16">
                                                                            <i class="ri-facebook-fill"></i>
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn-icon btn-info-light fs-16">
                                                                            <i class="ri-linkedin-fill"></i>
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn-icon btn-danger-light fs-16">
                                                                            <i class="ri-whatsapp-line"></i>
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            class="btn-icon btn-primary-light fs-16">
                                                                            <i class="ri-telegram-2-fill"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>Name</td>
                                                                                <td>
                                                                                    <div class="text-heading">
                                                                                        {{ $data->name ?? '' }}</div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Phone</td>
                                                                                <td>
                                                                                    <div class="text-heading">
                                                                                        {{ $data->phonecode }}
                                                                                        {{ $data->phone }}
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Email</td>
                                                                                <td>
                                                                                    <div class="text-heading">
                                                                                        {{ $data->email }}</div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Timezone</td>
                                                                                <td>
                                                                                    <div class="text-heading">
                                                                                        {{ $data->timezone }}</div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Created Date</td>
                                                                                <td>
                                                                                    <div class="text-heading">
                                                                                        {{ $data->created_at }}</div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Currency</td>
                                                                                <td>
                                                                                    <div class="badge bg-label-success">
                                                                                        {{ $data->currency ?? '-' }}</div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Last Login At</td>
                                                                                <td>
                                                                                    <div class="badge bg-label-success">
                                                                                        {{ $data->last_login_at ?? '-' }}
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>IP Address</td>
                                                                                <td>
                                                                                    <div class="badge bg-label-success">
                                                                                        {{ $data->ip_address ?? '-' }}
                                                                                    </div>
                                                                                </td>
                                                                            </tr>


                                                                        </tbody>
                                                                    </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-8">
                                                        <div class="row">
                                                            ....
                                                            {{-- <div class="col-xl-6 col-lg-3 col-md-6">
                                                                <div class="card">
                                                                    <div
                                                                        class="card-body mini-card-body d-flex align-center gap-16">
                                                                        <div
                                                                            class="avatar avatar-xl bg-primary-transparent text-primary">
                                                                            <i class="ri-shopping-bag-3-line fs-42"></i>
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <span class="d-block fs-16 mb-5">Total
                                                                                Orders</span>
                                                                            <h2 class="mb-5">98.5k</h2>
                                                                            <span class="text-success">+1.24%<i
                                                                                    class="ri-arrow-up-line ml-5 d-inline-block"></i></span>
                                                                            <span class="fs-12 text-muted ml-5">This
                                                                                week</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-6 col-lg-3 col-md-6">
                                                                <div class="card">
                                                                    <div
                                                                        class="card-body mini-card-body d-flex align-center gap-16">
                                                                        <div
                                                                            class="avatar avatar-xl bg-warning-transparent text-warning">
                                                                            <i class="ri-time-line fs-42"></i>
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <span class="d-block fs-16 mb-5">Pending
                                                                                Orders</span>
                                                                            <h2 class="mb-5">12</h2>
                                                                            <span class="text-warning">+2 pending<i
                                                                                    class="ri-arrow-up-line ml-5 d-inline-block"></i></span>
                                                                            <span class="fs-12 text-muted ml-5">In
                                                                                Dispatch</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-6 col-lg-3 col-md-6">
                                                                <div class="card">
                                                                    <div
                                                                        class="card-body mini-card-body d-flex align-center gap-16">
                                                                        <div
                                                                            class="avatar avatar-xl bg-success-transparent text-success">
                                                                            <i class="ri-checkbox-circle-line fs-42"></i>
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <span class="d-block fs-16 mb-5">Completed
                                                                                Orders</span>
                                                                            <h2 class="mb-5">86</h2>
                                                                            <span class="text-success">+8.5%<i
                                                                                    class="ri-arrow-up-line ml-5 d-inline-block"></i></span>
                                                                            <span class="fs-12 text-muted ml-5">This
                                                                                month</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-6 col-lg-3 col-md-6">
                                                                <div class="card">
                                                                    <div
                                                                        class="card-body mini-card-body d-flex align-center gap-16">
                                                                        <div
                                                                            class="avatar avatar-xl bg-purple-transparent text-purple">
                                                                            <i
                                                                                class="ri-money-dollar-circle-line fs-42"></i>
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <span class="d-block fs-16 mb-5">Total
                                                                                Spent</span>
                                                                            <h2 class="mb-5">$12,450</h2>
                                                                            <span class="text-success">+15%<i
                                                                                    class="ri-arrow-up-line ml-5 d-inline-block"></i></span>
                                                                            <span class="fs-12 text-muted ml-5">vs last
                                                                                year</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> --}}

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-notes" role="tabpanel"
                                                aria-labelledby="pills-notes-tab" tabindex="0">
                                                <form class="validate_form" action="{{ route('settings.update') }}"
                                                    method="POST" enctype="multipart/form-data" id="categoryForm">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="row">

                                                        <!-- Site Name -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Site Name</label>
                                                            <input type="text" name="site_name" class="form-control"
                                                                value="{{ old('site_name', $settings->site_name ?? '') }}"
                                                                placeholder="Enter site name">
                                                        </div>

                                                        <!-- Site Tagline -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Site Tagline</label>
                                                            <input type="text" name="site_tagline"
                                                                class="form-control"
                                                                value="{{ old('site_tagline', $settings->site_tagline ?? '') }}"
                                                                placeholder="Enter site tagline">
                                                        </div>

                                                        <!-- Email -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Admin Email</label>
                                                            <input type="email" name="email" class="form-control"
                                                                value="{{ old('email', $settings->email ?? '') }}"
                                                                placeholder="Enter email address">
                                                        </div>

                                                        <!-- Phone -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Phone</label>
                                                            <input type="text" name="phone" class="form-control"
                                                                value="{{ old('phone', $settings->phone ?? '') }}"
                                                                placeholder="Enter phone number">
                                                        </div>

                                                        <!-- Address -->
                                                        <div class="col-md-12 mb-15">
                                                            <label class="form-label">Address</label>
                                                            <textarea name="address" class="form-control" rows="3" placeholder="Enter address">{{ old('address', $settings->address ?? '') }}</textarea>
                                                        </div>

                                                        <!-- Site Logo -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Site Logo</label>
                                                            <input type="file" name="site_logo"
                                                                class="form-control imageInput" data-preview="previewLogo"
                                                                accept="image/*">

                                                            <div class="preview-container {{ empty($settings->site_logo) ? 'd-none' : '' }}"
                                                                id="wrap_previewLogo">
                                                                <img id="previewLogo" class="img-preview"
                                                                    src="{{ isset($settings->site_logo) ? asset($settings->site_logo) : '' }}">
                                                            </div>
                                                        </div>

                                                        <!-- Favicon -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Favicon</label>
                                                            <input type="file" name="site_favicon"
                                                                class="form-control imageInput"
                                                                data-preview="previewFavicon" accept="image/*">

                                                            <div class="preview-container {{ empty($settings->site_favicon) ? 'd-none' : '' }}"
                                                                id="wrap_previewFavicon">
                                                                <img id="previewFavicon" class="img-preview"
                                                                    src="{{ isset($settings->site_favicon) ? asset($settings->site_favicon) : '' }}">
                                                            </div>
                                                        </div>

                                                        <!-- Meta Title -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Meta Title</label>
                                                            <input type="text" name="meta_title" class="form-control"
                                                                value="{{ old('meta_title', $settings->meta_title ?? '') }}"
                                                                placeholder="SEO meta title">
                                                        </div>

                                                        <!-- Meta Keywords -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Meta Keywords</label>
                                                            <input type="text" name="meta_keywords"
                                                                class="form-control"
                                                                value="{{ old('meta_keywords', $settings->meta_keywords ?? '') }}"
                                                                placeholder="keyword1, keyword2, keyword3">
                                                        </div>

                                                        <!-- Meta Description -->
                                                        <div class="col-md-12 mb-15">
                                                            <label class="form-label">Meta Description</label>
                                                            <textarea name="meta_description" class="form-control" rows="3" placeholder="SEO meta description">{{ old('meta_description', $settings->meta_description ?? '') }}</textarea>
                                                        </div>

                                                        <!-- Social Links -->
                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Facebook</label>
                                                            <input type="url" name="facebook" class="form-control"
                                                                value="{{ old('facebook', $settings->facebook ?? '') }}">
                                                        </div>

                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Instagram</label>
                                                            <input type="url" name="instagram" class="form-control"
                                                                value="{{ old('instagram', $settings->instagram ?? '') }}">
                                                        </div>

                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">Twitter</label>
                                                            <input type="url" name="twitter" class="form-control"
                                                                value="{{ old('twitter', $settings->twitter ?? '') }}">
                                                        </div>

                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">LinkedIn</label>
                                                            <input type="url" name="linkedin" class="form-control"
                                                                value="{{ old('linkedin', $settings->linkedin ?? '') }}">
                                                        </div>

                                                        <div class="col-md-6 mb-15">
                                                            <label class="form-label">YouTube</label>
                                                            <input type="url" name="youtube" class="form-control"
                                                                value="{{ old('youtube', $settings->youtube ?? '') }}">
                                                        </div>

                                                        <!-- Maintenance Mode -->
                                                        <div class="col-md-12 mb-15">
                                                            <div class="form-check mt-2">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="maintenance_mode" value="1"
                                                                    {{ old('maintenance_mode', $settings->maintenance_mode ?? false) ? 'checked' : '' }}>
                                                                <label class="form-check-label">
                                                                    Enable Maintenance Mode
                                                                </label>
                                                            </div>
                                                        </div>

                                                    </div>


                                                    <div class="mt-3">
                                                        <button type="submit" class="btn btn-primary">Submit</button>

                                                    </div>

                                                </form>
                                            </div>
                                            <div class="tab-pane fade" id="pills-calls" role="tabpanel"
                                                aria-labelledby="pills-calls-tab" tabindex="0">
                                                Call...
                                            </div>
                                            <div class="tab-pane fade" id="pills-email" role="tabpanel"
                                                aria-labelledby="pills-email-tab" tabindex="0">
                                                ...123
                                            </div>
                                            <div class="tab-pane fade" id="pills-task" role="tabpanel"
                                                aria-labelledby="pills-task-tab" tabindex="0">
                                                456...
                                            </div>
                                            <div class="tab-pane fade" id="pills-files" role="tabpanel"
                                                aria-labelledby="pills-files-tab" tabindex="0">
                                                1111...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
