@extends('layouts.admin_auth')
<style>
    input#password {
        width: 80%;
    }
</style>
@section('content')
    <div class="authentication-wrapper basic-authentication">
        <div class="authentication-inner">
            <div class="card">

                <!-- Top Section -->
                <div class="authentication-top text-center mb-25">
                    <a href="javascript:;" class="authentication-logo logo-black"
                        style="background: #edeef2;
    padding: 10px;     border-radius: 8px;">
                        <img src="{{ URL::asset('default_images/logo.png') }}" alt="logo">
                    </a>
                    <a href="javascript:;" class="authentication-logo logo-white">
                        <img src="{{ URL::asset('default_images/logo.png') }}" alt="logo">
                    </a>
                    <h4 class="mb-5">Welcome to GR Silver Designs</h4>
                    <p>Please sign-in to your account and start the adventure</p>
                </div>

                <!-- Login Form -->
                <form class="validate_form" id="validate_form" action="{{ route('admin.login') }}" method="POST">
                    @csrf

                    <!-- Email -->
                    <div class="mb-10">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Enter Email" id="email">
                    </div>

                    <!-- Password -->
                    <div class="mb-15">
                        <div class="d-flex justify-content-between mb-5">
                            <label for="password" class="form-label mb-0">Password</label>
                            <a class="text-body" href="{{ route('admin.password.request') }}">Forgot Password?</a>
                        </div>

                        <div class="input-group input-password-wrapper">
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="Enter Password">
                            <button class="input-group-text text-black toggle-password" type="button">
                                <i class="ri-eye-line fs-20"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button class="btn btn-primary w-100" type="submit">Sign in</button>
                </form>

                <!-- Info Text -->
                <div class="alert alert-light border text-center mb-0 py-2 mt-4">
                    <small class="text-muted ">
                        <i class="ri-shield-check-line me-1"></i>
                        Restricted access for authorized personnel only
                    </small>
                </div>

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

@push('scripts')
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordInput = this.closest('.input-group').querySelector('input');
                const icon = this.querySelector('i');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
                }
            });
        });
    </script>
@endpush
