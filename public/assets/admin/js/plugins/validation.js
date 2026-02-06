// =============================
// GLOBAL VALIDATION + AJAX + MODAL SUPPORT
// =============================

/**
 * Button Loader Utilities
 */
function startButtonLoader($btn) {
    if (!$btn || !$btn.length) return;

    $btn.prop("disabled", true)
        .addClass("btn-disabled")
        .data("original-text", $btn.html())
        .html('<span class="text">Processing...</span>');
}

function stopButtonLoader($btn) {
    if (!$btn || !$btn.length) return;

    const originalText = $btn.data("original-text");
    if (originalText) {
        $btn.html(originalText);
    }

    $btn.prop("disabled", false)
        .removeClass("btn-disabled");
}

/**
 * Display Backend Validation Errors
 */
function displayBackendErrors(errors, form) {
    if (!errors || typeof errors !== 'object') return;

    Object.keys(errors).forEach(fieldName => {
        const errorMessages = Array.isArray(errors[fieldName])
            ? errors[fieldName]
            : [errors[fieldName]];

        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');

            // Remove existing error message
            const existingError = field.parentElement.querySelector('.just-validate-error-label');
            if (existingError) {
                existingError.remove();
            }

            // Add new error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'just-validate-error-label';
            errorDiv.textContent = errorMessages[0];
            field.parentElement.appendChild(errorDiv);
        }
    });
}

/**
 * Clear all validation errors
 */
function clearValidationErrors(form) {
    form.querySelectorAll('.is-valid, .is-invalid').forEach(field => {
        field.classList.remove('is-valid', 'is-invalid');
    });

    form.querySelectorAll('.just-validate-error-label').forEach(errorLabel => {
        errorLabel.remove();
    });
}

document.addEventListener("DOMContentLoaded", function () {

    // ---------- VALIDATION RULES ----------
    window.RULES = {
        email: [
            { rule: 'required', errorMessage: 'Email is required' },
            { rule: 'email', errorMessage: 'Enter a valid email' },
            { rule: 'maxLength', value: 50, errorMessage: 'Email must not exceed 50 characters' }
        ],

        role: [
            { rule: 'required', errorMessage: 'Role is required' }
        ],

        name: [
            { rule: 'required', errorMessage: 'Name is required' },
            { rule: 'minLength', value: 3, errorMessage: 'Name must be at least 3 characters' },
            { rule: 'maxLength', value: 30, errorMessage: 'Name must not exceed 30 characters' }
        ],

        phone: [
            { rule: 'required', errorMessage: 'Phone is required' },
            {
                rule: 'customRegexp',
                value: /^[0-9]{10,15}$/,
                errorMessage: 'Phone must be 10-15 digits'
            }
        ],

        parent_selling_price: [
            { rule: 'required', errorMessage: 'Selling price is required' },
            { rule: 'number', errorMessage: 'Selling price must be a number' },
            { rule: 'minNumber', value: 1, errorMessage: 'Selling price must be greater than 0' }
        ],

        parent_mrp_price: [
            { rule: 'required', errorMessage: 'MRP price is required' },
            { rule: 'number', errorMessage: 'MRP price must be a number' },
            { rule: 'minNumber', value: 1, errorMessage: 'MRP price must be greater than 0' }
        ],

        password: [
            { rule: 'required', errorMessage: 'Password is required' },
            { rule: 'minLength', value: 6, errorMessage: 'Password must be at least 6 characters' },
            { rule: 'customRegexp', value: /[a-z]/, errorMessage: 'Must contain lowercase letter' },
            { rule: 'customRegexp', value: /[A-Z]/, errorMessage: 'Must contain uppercase letter' },
            { rule: 'customRegexp', value: /[0-9]/, errorMessage: 'Must contain number' },
            {
                rule: 'customRegexp',
                value: /[!@#$%^&*(),.?":{}|<>]/,
                errorMessage: 'Must contain special character'
            },
            { rule: 'customRegexp', value: /^\S+$/, errorMessage: 'No spaces allowed' }
        ],

        password_confirmation: [
            { rule: 'required', errorMessage: 'Password confirmation is required' },
            {
                rule: 'custom',
                validator: (value) => {
                    const passwordField = document.querySelector('[name="password"]');
                    return passwordField ? value === passwordField.value : false;
                },
                errorMessage: 'Passwords must match'
            }
        ],

        category_id: [
            { rule: 'required', errorMessage: 'Category is required' }
        ],

        sub_category_id: [
            { rule: 'required', errorMessage: 'Sub-category is required' }
        ],

        metal_id: [
            { rule: 'required', errorMessage: 'Metal is required' }
        ],

        status: [
            { rule: 'required', errorMessage: 'Status is required' }
        ],

        product_name: [
            { rule: 'required', errorMessage: 'Product name is required' },
            { rule: 'minLength', value: 3, errorMessage: 'Product name must be at least 3 characters' },
            { rule: 'maxLength', value: 150, errorMessage: 'Product name must not exceed 150 characters' }
        ],

        title: [
            { rule: 'required', errorMessage: 'Title is required' },
            { rule: 'minLength', value: 3, errorMessage: 'Title must be at least 3 characters' },
            { rule: 'maxLength', value: 250, errorMessage: 'Title must not exceed 250 characters' }
        ],

        content: [
            { rule: 'required', errorMessage: 'Content is required' },
            { rule: 'minLength', value: 3, errorMessage: 'Content must be at least 3 characters' }
        ],

        slug: [
            { rule: 'required', errorMessage: 'Slug is required' },
            {
                rule: 'customRegexp',
                value: /^[a-z0-9]+(?:-[a-z0-9]+)*$/,
                errorMessage: 'Slug must be lowercase letters, numbers, and hyphens only'
            },
            { rule: 'maxLength', value: 180, errorMessage: 'Slug must not exceed 180 characters' }
        ],

        sku: [
            { rule: 'required', errorMessage: 'SKU is required' },
            { rule: 'minLength', value: 3, errorMessage: 'SKU must be at least 3 characters' },
            { rule: 'maxLength', value: 50, errorMessage: 'SKU must not exceed 50 characters' }
        ],

        main_image: [
            { rule: 'required', errorMessage: 'Main image is required' },
            {
                rule: 'files',
                value: {
                    files: {
                        maxSize: 5 * 1024 * 1024,
                        extensions: ['jpg', 'jpeg', 'png', 'webp']
                    }
                },
                errorMessage: 'Max 5MB, allowed: JPG, PNG, WebP'
            }
        ],

        secondary_image: [
            { rule: 'required', errorMessage: 'Secondary image is required' },
            {
                rule: 'files',
                value: {
                    files: {
                        maxSize: 5 * 1024 * 1024,
                        extensions: ['jpg', 'jpeg', 'png', 'webp']
                    }
                },
                errorMessage: 'Max 5MB, allowed: JPG, PNG, WebP'
            }
        ],

        short_description: [
            { rule: 'required', errorMessage: 'Short description is required' },
            { rule: 'minLength', value: 80, errorMessage: 'Short description must be at least 80 characters' }
        ],

        description: [
            { rule: 'required', errorMessage: 'Description is required' },
            { rule: 'minLength', value: 10, errorMessage: 'Description must be at least 10 characters' }
        ],

        code: [
            { rule: 'required', errorMessage: 'Code is required' },
            { rule: 'minLength', value: 3, errorMessage: 'Code must be at least 3 characters' },
            { rule: 'maxLength', value: 100, errorMessage: 'Code must not exceed 100 characters' }
        ],

        type: [
            { rule: 'required', errorMessage: 'Type is required' }
        ],

        value: [
            { rule: 'required', errorMessage: 'Value is required' }
        ],

        user_limit: [
            { rule: 'required', errorMessage: 'User limit is required' }
        ],

        min_purchase_amount: [
            { rule: 'required', errorMessage: 'Minimum purchase amount is required' }
        ],

        seo_title: [
            { rule: 'maxLength', value: 60, errorMessage: 'SEO title must not exceed 60 characters' }
        ],

        seo_description: [
            { rule: 'maxLength', value: 160, errorMessage: 'SEO description must not exceed 160 characters' }
        ],

        seo_keywords: [
            { rule: 'maxLength', value: 255, errorMessage: 'SEO keywords must not exceed 255 characters' }
        ]
    };

    // ---------- VALIDATOR INITIALIZER ----------
    window.initializeValidator = function () {
        document.querySelectorAll('.validate_form').forEach(form => {

            // Prevent duplicate initialization
            if (form.dataset.validationInitialized) return;
            form.dataset.validationInitialized = "true";

            const validator = new JustValidate(form, {
                errorFieldCssClass: 'is-invalid',
                successFieldCssClass: 'is-valid',
                focusInvalidField: true,
                lockForm: true
            });

            // Add validation rules (skip duplicates)
            const addedFields = new Set();

            form.querySelectorAll('[name]').forEach(input => {
                const fieldName = input.name;

                // Skip if already added or no rules defined
                if (addedFields.has(fieldName) || !RULES[fieldName]) return;

                validator.addField(`[name="${fieldName}"]`, RULES[fieldName]);
                addedFields.add(fieldName);
            });

            // ---------- AJAX FORM SUBMISSION ----------
            validator.onSuccess(async (event) => {
                event.preventDefault();

                const action = form.action;
                const method = form.method || 'POST';
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                const submitBtn = form.querySelector('[type="submit"]');
                const $submitBtn = submitBtn ? $(submitBtn) : null;

                // Clear previous errors
                clearValidationErrors(form);

                // Start loading
                startButtonLoader($submitBtn);

                try {
                    const response = await fetch(action, {
                        method: method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                        }
                    });

                    const contentType = response.headers.get('content-type');
                    let data = {};

                    // Handle JSON response
                    if (contentType && contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        // Non-JSON response (could be HTML error page)
                        const text = await response.text();
                        console.error('Non-JSON response:', text);
                        throw new Error('Invalid server response');
                    }

                    // Handle successful response
                    if (response.ok && data.status === true) {

                        // Show success message
                        if (typeof successToast === 'function') {
                            successToast(data.message || 'Action completed successfully!');
                        } else {
                            alert(data.message || 'Action completed successfully!');
                        }

                        // Close modal if open
                        const modal = $('#app_gloval_modal');
                        if (modal.length && modal.hasClass('show')) {
                            modal.modal('hide');
                            modal.find('.modal-content').html('');
                        }

                        // Reload DataTable if exists
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#dataTable')) {
                            $('#dataTable').DataTable().ajax.reload(null, false);
                        }

                        // Redirect if URL provided
                        if (data.redirect_url) {
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 500);
                        }

                    }
                    // Handle validation errors (422 status)
                    else if (response.status === 422 && data.errors) {
                        displayBackendErrors(data.errors, form);

                        if (typeof errorToast === 'function') {
                            errorToast(data.message || 'Please fix the validation errors');
                        }
                    }
                    // Handle other errors
                    else {
                        const errorMessage = data.message || 'Something went wrong. Please try again.';

                        if (typeof errorToast === 'function') {
                            errorToast(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    }

                } catch (error) {
                    console.error('Form submission error:', error);

                    const errorMessage = 'Network error. Please check your connection and try again.';

                    if (typeof errorToast === 'function') {
                        errorToast(errorMessage);
                    } else {
                        alert(errorMessage);
                    }

                } finally {
                    // Always stop loading state
                    stopButtonLoader($submitBtn);
                }
            });
        });
    };

    // ---------- INITIALIZE ON PAGE LOAD ----------
    initializeValidator();
});

// ---------- MODAL EVENT HANDLERS ----------
$(document).on('shown.bs.modal', '#app_gloval_modal', function () {
    // Initialize validation for forms inside modal
    initializeValidator();
});

$(document).on('hidden.bs.modal', '#app_gloval_modal', function () {
    // Clear validation states when modal is closed
    const form = $(this).find('.validate_form')[0];
    if (form) {
        clearValidationErrors(form);
    }
});

// ---------- RESET FORM HANDLER ----------
$(document).on('reset', '.validate_form', function () {
    clearValidationErrors(this);
});