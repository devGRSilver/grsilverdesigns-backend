<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? '' }}</h5>
    </div>

    <div class="card-body pt-15">
        <form class="validate_form" action="{{ route('staff.update', encrypt($data->id)) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                <!-- Role -->
                <div class="col-md-12 mb-15">
                    <label class="form-label">
                        Role <span class="text-danger">*</span>
                    </label>
                    <select name="role" class="form-select" required>
                        <option value="">Select Role</option>

                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ $data->roles->first()?->name === $role->name ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Full Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name"
                        value="{{ old('name', $data->name) }}">
                </div>

                <!-- Phone -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="Enter phone number"
                        value="{{ old('phone', $data->phone) }}">
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="{{ $data->email }}" name="email" readonly>
                </div>



            </div>

            <button type="submit" class="btn btn-primary">
                Update Details
            </button>

            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Close
            </button>

        </form>
    </div>
</div>
