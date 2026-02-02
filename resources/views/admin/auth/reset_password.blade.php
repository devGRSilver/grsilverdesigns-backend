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
                    <h4 class="mb-5">Set Your New Password</h4>
                    <p>Please create a strong password to secure your admin account.</p>
                </div>

                <!-- Reset Password Form -->
                <form class="validate_form" action="{{ route('admin.password.update', $token) }}" method="POST">
                    @csrf

                    <!-- Hidden token and email -->
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <!-- New Password -->
                    <div class="mb-15">
                        <label class="form-label">New Password</label>
                        <div class="input-group input-password-wrapper">
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="Enter your new password" required>
                            <button class="input-group-text toggle-password" type="button">
                                <i class="ri-eye-line fs-20"></i>
                            </button>
                        </div>

                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-25">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-group input-password-wrapper">
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Re-enter new password" required>
                            <button class="input-group-text toggle-password" type="button">
                                <i class="ri-eye-line fs-20"></i>
                            </button>
                        </div>

                        @error('password_confirmation')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Update Password</button>

                    <a href="{{ route('admin.login') }}" class="btn btn-secondary w-100 mt-4">
                        <i class="ri-arrow-left-line me-1"></i> Back to Login
                    </a>


                </form>

                <!-- Info Text -->
                <p class="text-center text-muted mt-3 px-2">
                    This admin panel is restricted to authorized management only.
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
