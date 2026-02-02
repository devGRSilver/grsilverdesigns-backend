<div class="card">



    <div class="card-header">
        <h5 class="mb-0">Change Password</h5>
    </div>

    <div class="card-body pt-15">
        <form class="validate_form" action="{{ route('users.password.update', $user_id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- New Password -->
            <div class="mb-15">
                <label class="form-label">New Password</label>
                <div class="input-group input-password-wrapper">
                    <input type="password" name="password" class="form-control" id="password"
                        placeholder="Enter Password">
                    <button class="input-group-text toggle-password" type="button">
                        <i class="ri-eye-line fs-20"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-25">
                <label class="form-label">Confirm New Password</label>
                <div class="input-group input-password-wrapper">
                    <input type="password" name="password_confirmation" class="form-control" id="confirmPassword"
                        placeholder="Confirm password">
                    <button class="input-group-text toggle-password" type="button">
                        <i class="ri-eye-line fs-20"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Password</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </form>
    </div>
</div>

<script>
    // Toggle password visibility for all fields with .toggle-password
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
