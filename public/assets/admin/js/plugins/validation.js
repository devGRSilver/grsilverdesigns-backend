// =============================
// GLOBAL VALIDATION + AJAX + MODAL SUPPORT
// =============================

function startButtonLoader($btn) {
    if (!$btn || !$btn.length) return;

    $btn.prop("disabled", true);
    $btn.addClass("btn-disabled");
    $btn.data("original-text", $btn.html());
    $btn.html('<span class="text">Processing...</span>');
}

function stopButtonLoader($btn) {
    if (!$btn || !$btn.length) return;

    const original = $btn.data("original-text");
    if (original) $btn.html(original);

    $btn.prop("disabled", false);
    $btn.removeClass("btn-disabled");
}

document.addEventListener("DOMContentLoaded", function () {

    // ---------- VALIDATION RULES ----------
    window.RULES = {
        email: [
            { rule: 'required', errorMessage: 'Email is required' },
            { rule: 'email', errorMessage: 'Enter a valid email' },
            { rule: 'maxLength', value: 50 }
        ],

        role: [{ rule: 'required', errorMessage: 'Role is required' }],

        name: [
            { rule: 'required', errorMessage: 'Name is required' },
            { rule: 'minLength', value: 3 },
            { rule: 'maxLength', value: 30 }
        ],

        phone: [
            { rule: 'required', errorMessage: 'Phone is required' },
            { rule: 'customRegexp', value: /^[0-9]{10,15}$/, errorMessage: 'Phone must be 10–15 digits' }
        ],


        parent_selling_price: [
            { rule: 'required', errorMessage: 'Selling price is required' },
            { rule: 'number', errorMessage: 'Selling price must be a number' },
            { rule: 'minNumber', value: 1, errorMessage: 'Selling price must be greater than 0' },
        ],

        parent_mrp_price: [
            { rule: 'required', errorMessage: 'MRP price is required' },
            { rule: 'number', errorMessage: 'MRP price must be a number' },
            { rule: 'minNumber', value: 1, errorMessage: 'MRP price must be greater than 0' },
        ],



        phone: [
            { rule: 'required', errorMessage: 'Phone is required' },
            { rule: 'customRegexp', value: /^[0-9]{10,15}$/, errorMessage: 'Phone must be 10–15 digits' }
        ],



        password: [
            { rule: 'required' },
            { rule: 'minLength', value: 6 },
            { rule: 'customRegexp', value: /[a-z]/, errorMessage: 'Must contain lowercase' },
            { rule: 'customRegexp', value: /[A-Z]/, errorMessage: 'Must contain uppercase' },
            { rule: 'customRegexp', value: /[0-9]/, errorMessage: 'Must contain number' },
            { rule: 'customRegexp', value: /[!@#$%^&*(),.?":{}|<>]/, errorMessage: 'Must contain special char' },
            { rule: 'customRegexp', value: /^\S+$/, errorMessage: 'No spaces allowed' }
        ],

        password_confirmation: [
            { rule: 'required' },
            {
                rule: 'custom',
                validator: value =>
                    value === document.querySelector('[name="password"]')?.value,
                errorMessage: 'Passwords must match'
            }
        ],

        category_id: [{ rule: 'required' }],
        sub_category_id: [{ rule: 'required' }],
        metal_id: [{ rule: 'required' }],
        status: [{ rule: 'required' }],

        product_name: [
            { rule: 'required' },
            { rule: 'minLength', value: 3 },
            { rule: 'maxLength', value: 150 }
        ],

        title: [
            { rule: 'required' },
            { rule: 'minLength', value: 3 },
            { rule: 'maxLength', value: 250 }
        ],

        content: [
            { rule: 'required' },
            { rule: 'minLength', value: 3 }
        ],

        slug: [
            { rule: 'required' },
            { rule: 'customRegexp', value: /^[a-z0-9]+(?:-[a-z0-9]+)*$/ },
            { rule: 'maxLength', value: 180 }
        ],

        sku: [
            { rule: 'required' },
            { rule: 'minLength', value: 3 },
            { rule: 'maxLength', value: 50 }
        ],

        main_image: [
            { rule: 'required' },
            {
                rule: 'files',
                value: {
                    files: {
                        maxSize: 5 * 1024 * 1024,
                        extensions: ['jpg', 'jpeg', 'png', 'webp']
                    }
                },
                errorMessage: 'Max 5MB JPG/PNG/WebP'
            }
        ],

        secondary_image: [
            { rule: 'required' },
            {
                rule: 'files',
                value: {
                    files: {
                        maxSize: 5 * 1024 * 1024,
                        extensions: ['jpg', 'jpeg', 'png', 'webp']
                    }
                },
                errorMessage: 'Max 5MB JPG/PNG/WebP'
            }
        ],

        short_description: [
            { rule: 'required' },
            { rule: 'minLength', value: 80 },
            { rule: 'maxLength', value: 100000 }
        ],

        description: [
            { rule: 'required' },
            { rule: 'minLength', value: 100 },
            { rule: 'maxLength', value: 500000 }
        ],

        seo_title: [{ rule: 'maxLength', value: 60 }],
        seo_description: [{ rule: 'maxLength', value: 160 }],
        seo_keywords: [{ rule: 'maxLength', value: 255 }]
    };

    // ---------- INITIALIZER ----------
    window.initializeValidator = function () {

        document.querySelectorAll('.validate_form').forEach(form => {

            if (form.dataset.validationInitialized) return;
            form.dataset.validationInitialized = "true";

            const validator = new JustValidate(form, {
                errorFieldCssClass: 'is-invalid',
                successFieldCssClass: 'is-valid',
                focusInvalidField: true
            });

            // ADD RULES (skip duplicate names like images[])
            const added = new Set();

            form.querySelectorAll('[name]').forEach(input => {
                if (added.has(input.name)) return;
                if (RULES[input.name]) {
                    validator.addField(`[name="${input.name}"]`, RULES[input.name]);
                    added.add(input.name);
                }
            });

            // ---------- AJAX SUBMIT ----------
            validator.onSuccess(async () => {

                const action = form.action;
                const method = form.method || 'POST';
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                const submitBtn = form.querySelector('[type="submit"]');
                const $submitBtn = submitBtn ? $(submitBtn) : null;

                startButtonLoader($submitBtn);

                try {
                    const response = await fetch(action, {
                        method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                        }
                    });

                    let data = {};
                    if (response.ok) {
                        data = await response.json();
                    } else {
                        throw new Error('Server error');
                    }

                    if (data.status === true) {

                        successToast(data.message || 'Action successful!');

                        const modal = $('#app_gloval_modal');
                        if (modal.hasClass('show')) {
                            modal.modal('hide');
                            modal.find('.modal-content').html('');
                        }

                        if ($.fn.DataTable?.isDataTable('#dataTable')) {
                            $('#dataTable').DataTable().ajax.reload(null, false);
                        }

                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }

                    } else {
                        errorToast(data.message || 'Something went wrong!');
                    }

                } catch (error) {
                    console.error('AJAX Error:', error);
                    errorToast('Failed to submit form. Please try again.');
                } finally {
                    stopButtonLoader($submitBtn);
                }
            });
        });
    };

    // ---------- PAGE LOAD ----------
    initializeValidator();
});

// ---------- MODAL SUPPORT ----------
$(document).on('shown.bs.modal', '#app_gloval_modal', function () {
    initializeValidator();
});

$(document).on('hidden.bs.modal', '#app_gloval_modal', function () {
    $('.validate_form')
        .find('.is-valid, .is-invalid')
        .removeClass('is-valid is-invalid');
});
