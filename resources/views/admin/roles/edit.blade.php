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

                                    <li class="breadcrumb-item">
                                        <a href="{{ route('roles.index') }}">Roles </a>
                                    </li>

                                    <li class="breadcrumb-item active"> Update </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>


                <form action="{{ route('roles.update', encrypt($role->id)) }}" method="POST" id="roleForm"
                    class="validate_form">
                    @csrf
                    @method('PUT')

                    <!-- Hidden input to ensure permissions is always in request -->
                    <input type="hidden" name="permissions" value="">

                    <div class="row g-4">
                        <!-- LEFT COLUMN: PERMISSION GROUPS -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                                <div class="card-header bg-gradient-primary text-white py-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-wrapper bg-white bg-opacity-20 rounded-circle p-2">
                                                <i class="bi bi-shield-check fs-5"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 fw-bold">Permission Settings</h5>
                                                <small class="opacity-75">Update access permissions for this role</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <label class="form-check-label text-white opacity-75 me-2">
                                                Select All
                                            </label>
                                            <div class="form-check form-switch m-0">
                                                <input type="checkbox" id="select-all-permissions"
                                                    class="form-check-input shadow-none" role="switch">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <!-- Role Name -->
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter role name" value="{{ old('name', $role->display_name) }}"
                                            required>
                                        @error('name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Permission Groups -->
                                    @foreach ($groups as $groupIndex => $group)
                                        <div class="permission-group mb-5">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div>
                                                        <h6 class="mt-4 fw-bold text-dark">
                                                            {{ $group->display_name ?? $group->name }}</h6>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <label class="form-check-label text-muted small me-2">
                                                        Select All
                                                    </label>
                                                    <div class="form-check form-switch m-0">
                                                        <input type="checkbox"
                                                            class="form-check-input group-select shadow-none"
                                                            data-group="{{ $group->id }}"
                                                            id="select-group-{{ $group->id }}" role="switch">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                @foreach ($group->permissions as $permissionIndex => $permission)
                                                    <div class="col-md-4">
                                                        <div class="permission-card border rounded-3 h-100">
                                                            <div class="card-body p-4">
                                                                <div class="form-check">
                                                                    <input type="checkbox" name="permissions[]"
                                                                        class="form-check-input permission-checkbox"
                                                                        value="{{ $permission->id }}"
                                                                        data-group="{{ $group->id }}"
                                                                        id="permission-{{ $permission->id }}"
                                                                        {{ in_array($permission->id, old('permissions', $rolePermissions ?? [])) ? 'checked' : '' }}>
                                                                    <label for="permission-{{ $permission->id }}"
                                                                        class="form-check-label d-flex align-items-center justify-content-between w-100">
                                                                        <div>
                                                                            <div
                                                                                class="d-flex align-items-center gap-2 mb-2">
                                                                                <i
                                                                                    class="bi bi-check-circle text-success opacity-25"></i>
                                                                                <span class="fw-semibold">
                                                                                    {{ $permission->display_name ?? $permission->name }}
                                                                                </span>
                                                                            </div>
                                                                            <small class="text-muted d-block">
                                                                                {{ $permission->description ?? 'No description available' }}
                                                                            </small>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: SELECTED OUTPUT -->
                        <div class="col-lg-4">
                            <div class="sticky-top" style="top: 20px;">
                                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                                    <div class="card-header bg-gradient-info text-white py-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-wrapper bg-white bg-opacity-20 rounded-circle p-2">
                                                <i class="bi bi-list-check fs-5"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 fw-bold">Selected Permissions</h5>
                                                <small class="opacity-75">Review your selections</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body p-4">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center p-3 bg-gradient rounded-3"
                                                style="background: #e5e9ed;">
                                                <div>
                                                    <span class="text-muted fw-medium">Selected Count</span>
                                                    <div class="small text-muted">Out of {{ $totalPermissions ?? 0 }}
                                                        permissions</div>
                                                </div>
                                                <div class="position-relative">
                                                    <div class="bg-white rounded-circle p-2 shadow-sm">
                                                        <span id="selected-count" class="fs-3 fw-bold text-primary">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="selected-items-container border rounded-3 p-3"
                                            style="max-height: 300px; overflow-y: auto; background: #f8f9fa;">
                                            <div id="selected-permissions-list">
                                                <div class="text-center py-5">
                                                    <div class="mb-3">
                                                        <i class="bi bi-check-circle display-1 text-muted opacity-25"></i>
                                                    </div>
                                                    <h6 class="text-muted mb-2">No Permissions Selected</h6>
                                                    <p class="text-muted small mb-0">
                                                        Select permissions from the groups to see them here
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <small class="text-muted">
                                                Selected permissions will be applied to this role
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-top p-4">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm py-3">
                                                <i class="bi bi-save me-2"></i>
                                                Update Role
                                            </button>
                                            <a href="{{ route('roles.index') }}"
                                                class="btn btn-outline-secondary btn-lg py-3">
                                                <i class="bi bi-arrow-left me-2"></i>
                                                Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Get all permission checkboxes
            const permissionCheckboxes = $('.permission-checkbox');
            const totalPermissions = permissionCheckboxes.length;

            // Store selected permissions with their data
            const selectedPermissions = new Map();

            // Debug function to log checked permissions
            function debugCheckedPermissions() {
                const checked = permissionCheckboxes.filter(':checked').map(function() {
                    return $(this).val();
                }).get();
                return checked;
            }

            // Initialize checkbox states
            function initializeCheckboxStates() {

                permissionCheckboxes.each(function() {
                    const $checkbox = $(this);
                    const permissionId = $checkbox.val();
                    const $card = $checkbox.closest('.permission-card');
                    const $icon = $checkbox.siblings('label').find('.bi-check-circle');

                    if ($checkbox.is(':checked')) {
                        // Update UI
                        $card.addClass('active border-primary');
                        $icon.removeClass('opacity-25 text-success').addClass('opacity-100 text-primary');

                        // Add to selectedPermissions if not already present
                        if (!selectedPermissions.has(permissionId)) {
                            const permissionName = $checkbox.siblings('label').find('.fw-semibold').text();
                            const groupName = $checkbox.closest('.permission-group').find('h6').text();
                            selectedPermissions.set(permissionId, {
                                id: permissionId,
                                name: permissionName,
                                group: groupName
                            });
                        }
                    } else {
                        // Update UI
                        $card.removeClass('active border-primary');
                        $icon.removeClass('opacity-100 text-primary').addClass('opacity-25 text-success');

                        // Remove from selectedPermissions
                        if (selectedPermissions.has(permissionId)) {
                            selectedPermissions.delete(permissionId);
                        }
                    }
                });

                debugCheckedPermissions();
            }

            // Update selected permissions list
            function updateSelectedPermissionsList() {
                const selectedList = $('#selected-permissions-list');
                const selectedCount = $('#selected-count');

                if (selectedPermissions.size === 0) {
                    selectedList.html(`
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-check-circle display-1 text-muted opacity-25"></i>
                            </div>
                            <h6 class="text-muted mb-2">No Permissions Selected</h6>
                            <p class="text-muted small mb-0">
                                Select permissions from the groups to see them here
                            </p>
                        </div>
                    `);
                    selectedCount.text('0');
                    return;
                }

                let html = '';
                selectedPermissions.forEach((permission, id) => {
                    html += `
                        <div class="d-flex align-items-center justify-content-between p-3 mb-2 bg-white rounded-3 border">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <span class="fw-semibold">${permission.name}</span>
                                    <div class="small text-muted">${permission.group}</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-permission" 
                                    data-permission-id="${id}">
                                <i class="ri-close-large-line"></i>
                            </button>
                        </div>
                    `;
                });

                selectedList.html(html);
                selectedCount.text(selectedPermissions.size);

                // Add remove functionality
                $('.remove-permission').on('click', function(e) {
                    e.stopPropagation();
                    const permissionId = $(this).data('permission-id');
                    $(`#permission-${permissionId}`).prop('checked', false).trigger('change');
                });
            }

            // Update group selection states
            function updateGroupSelectionStates() {
                $('.group-select').each(function() {
                    const groupId = $(this).data('group');
                    const groupCheckboxes = $(`.permission-checkbox[data-group="${groupId}"]`);
                    const checkedCount = groupCheckboxes.filter(':checked').length;
                    const totalCount = groupCheckboxes.length;

                    // Update group checkbox state
                    this.checked = checkedCount === totalCount;
                    this.indeterminate = checkedCount > 0 && checkedCount < totalCount;
                });

                // Update select all checkbox
                const checkedAll = permissionCheckboxes.filter(':checked').length;
                const selectAllCheckbox = $('#select-all-permissions')[0];
                selectAllCheckbox.checked = checkedAll === totalPermissions;
                selectAllCheckbox.indeterminate = checkedAll > 0 && checkedAll < totalPermissions;

            }

            // Handle permission checkbox change
            permissionCheckboxes.on('change', function(e) {
                e.stopPropagation();

                const $checkbox = $(this);
                const permissionId = $checkbox.val();
                const isChecked = $checkbox.is(':checked');
                const permissionName = $checkbox.siblings('label').find('.fw-semibold').text();
                const groupName = $checkbox.closest('.permission-group').find('h6').text();


                if (isChecked) {
                    selectedPermissions.set(permissionId, {
                        id: permissionId,
                        name: permissionName,
                        group: groupName
                    });
                } else {
                    selectedPermissions.delete(permissionId);
                }

                // Update UI states
                const $card = $checkbox.closest('.permission-card');
                const $icon = $checkbox.siblings('label').find('.bi-check-circle');

                if (isChecked) {
                    $card.addClass('active border-primary');
                    $icon.removeClass('opacity-25 text-success').addClass('opacity-100 text-primary');
                } else {
                    $card.removeClass('active border-primary');
                    $icon.removeClass('opacity-100 text-primary').addClass('opacity-25 text-success');
                }

                updateSelectedPermissionsList();
                updateGroupSelectionStates();
                debugCheckedPermissions();
            });

            // Handle group select all
            $('.group-select').on('change', function() {
                const groupId = $(this).data('group');
                const isChecked = $(this).is(':checked');
                const groupCheckboxes = $(`.permission-checkbox[data-group="${groupId}"]`);


                // Update all checkboxes in the group
                groupCheckboxes.prop('checked', isChecked);

                // Trigger change event for each checkbox
                groupCheckboxes.each(function() {
                    $(this).trigger('change');
                });
            });

            // Handle select all permissions
            $('#select-all-permissions').on('change', function() {
                const isChecked = $(this).is(':checked');

                // Update all checkboxes
                permissionCheckboxes.prop('checked', isChecked);

                // Trigger change event for each checkbox
                permissionCheckboxes.each(function() {
                    $(this).trigger('change');
                });
            });

            // Make permission cards clickable
            $('.permission-card').on('click', function(e) {
                // Don't trigger if clicking on checkbox or remove button
                if (!$(e.target).closest('.form-check-input, .remove-permission, .btn').length) {
                    const $checkbox = $(this).find('.permission-checkbox');
                    const newState = !$checkbox.prop('checked');
                    $checkbox.prop('checked', newState).trigger('change');
                }
            });

            // Handle form submission
            $('#roleForm').on('submit', function(e) {
                const selectedCount = permissionCheckboxes.filter(':checked').length;
                const checkedPermissions = debugCheckedPermissions();



                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one permission for the role.');
                    return false;
                }

                // Log form data for debugging
                const formData = $(this).serialize();

                return true;
            });

            // Initialize on page load
            initializeCheckboxStates();
            updateSelectedPermissionsList();
            updateGroupSelectionStates();

            // Log initial state
        });
    </script>

    <style>
        .permission-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .permission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #adb5bd;
        }

        .permission-card.active {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .form-check-input:indeterminate {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .selected-items-container::-webkit-scrollbar {
            width: 6px;
        }

        .selected-items-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .selected-items-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .selected-items-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .permission-group {
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .permission-group:last-child {
            border-bottom: none;
        }
    </style>
@endpush
