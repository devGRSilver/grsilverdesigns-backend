@extends('layouts.admin_auth')

@section('content')
    <div class="authentication-wrapper basic-authentication">
        <div class="authentication-inner">
            <div class="card">

                <!-- Top Section -->
                <div class="authentication-top text-center mb-25">
                    <a href="javascript:;" class="authentication-logo logo-black">
                        <img src="{{ URL::asset('default_images/logo.png') }}" alt="logo">
                    </a>
                    <a href="javascript:;" class="authentication-logo logo-white">
                        <img src="{{ URL::asset('default_images/logo.png') }}" alt="logo">
                    </a>
                    <h4 class="mb-5">Forgot Password?</h4>
                    <p>Enter your email to receive the password reset link</p>
                </div>

                <!-- Forget Password Form -->
                <form class="validate_form" id="validate_form" action="{{ route('admin.password.email') }}" method="POST">
                    @csrf

                    <!-- Email -->
                    <div class="mb-10">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter Email" id="email"
                            required>
                    </div>

                    <!-- Submit -->
                    <button class="btn btn-primary w-100 mb-10" type="submit">
                        Send Reset Link
                    </button>

                    <!-- Back to Login -->
                    <a href="{{ route('admin.login') }}" class="btn btn-secondary w-100">
                        <i class="ri-arrow-left-line me-1"></i> Back to Login
                    </a>
                </form>

                <!-- Info Text -->
                <p class="text-center text-muted mt-3">
                    <span>Access restricted to authorized personnel only.</span><br>
                    <span>If you are Admin or Management Staff, please log in with your credentials.</span><br>
                    <span>Other users are not permitted to access this panel.</span>
                </p>

                <!-- Divider -->
                <div class="divider-wrapper">
                    <div class="divider-line left-line"></div>
                    <span class="divider-title">or</span>
                    <div class="divider-line"></div>
                </div>

                <!-- Social Buttons -->
                <div class="d-flex-center gap-15">
                    <a href="javascript:void(0);" class="btn-icon btn-dark-light fs-16"><i
                            class="ri-twitter-x-line"></i></a>
                    <a href="javascript:void(0);" class="btn-icon btn-success-light fs-16"><i
                            class="ri-facebook-fill"></i></a>
                    <a href="javascript:void(0);" class="btn-icon btn-info-light fs-16"><i class="ri-linkedin-fill"></i></a>
                    <a href="javascript:void(0);" class="btn-icon btn-danger-light fs-16"><i
                            class="ri-whatsapp-line"></i></a>
                    <a href="javascript:void(0);" class="btn-icon btn-teal-light fs-16"><i
                            class="ri-telegram-2-fill"></i></a>
                </div>

            </div>
        </div>
    </div>
@endsection
