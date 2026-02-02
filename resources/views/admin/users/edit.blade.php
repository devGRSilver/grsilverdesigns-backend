<div class="card">

    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? '' }}</h5>
    </div>

    <div class="card-body pt-15">
        <form class="validate_form" action="{{ route('users.update', encrypt($data->id)) }}" method="POST">
            @csrf


            @method('PUT')


            <div class="row">

                <!-- Full Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name"
                        value="{{ $data->name ?? '' }}">
                </div>

                <!-- Phone -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="Enter phone number"
                        value="{{ $data->phone ?? '' }}">
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email"
                        value="{{ $data->email ?? '' }}" readonly>
                </div>

                <!-- Timezone -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Timezone</label>
                    <select name="timezone" class="form-select" readonly>
                        <option value="{{ $data->timezone }}" selected>
                            {{ $data->timezone }}
                        </option>
                    </select>
                </div>

                <!-- Currency -->
                <div class="col-md-6 mb-25">
                    <label class="form-label">Currency</label>
                    <select name="currency" class="form-select" readonly>
                        <option value="{{ $data->currency }}" selected>
                            {{ $data->currency }}
                        </option>

                    </select>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Update Details</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </form>
    </div>
</div>
