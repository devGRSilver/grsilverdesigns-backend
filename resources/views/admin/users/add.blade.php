<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? '' }}</h5>
    </div>

    <div class="card-body pt-15">
        <form class="validate_form" action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="row">

                <!-- Full Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                </div>

                <!-- Phone -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" placeholder="Enter phone number" required>
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                </div>

                <!-- Password -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Password</label>
                    <div class="input-group input-password-wrapper">
                        <input type="password" name="password" class="form-control" id="password"
                            placeholder="Enter Password">
                        <button class="input-group-text toggle-password" type="button">
                            <i class="ri-eye-line fs-20"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group input-password-wrapper">
                        <input type="password" name="password_confirmation" class="form-control" id="confirmPassword"
                            placeholder="Confirm password">
                        <button class="input-group-text toggle-password" type="button">
                            <i class="ri-eye-line fs-20"></i>
                        </button>
                    </div>
                </div>

            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </form>
    </div>
</div>

<script>
    // Password show/hide toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
            } else {
                input.type = 'password';
                icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
            }
        });
    });
</script>
