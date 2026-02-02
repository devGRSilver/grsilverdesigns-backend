// =============================
// GLOBAL VALIDATION + AJAX + MODAL SUPPORT
// =============================


function startButtonLoader(btn) {
    btn.prop("disabled", true);
    btn.addClass("btn-disabled");
    btn.data("original-text", btn.html());
    btn.html('<span class="text">Processing...</span>');
}

function stopButtonLoader(btn) {
    btn.html(btn.data("original-text"));
    btn.prop("disabled", false);
    btn.removeClass("btn-disabled");
}


document.addEventListener("DOMContentLoaded", function () {
    // ---------- ALL RULES ----------
    window.RULES = {

        email: [
            { rule: 'required', errorMessage: 'Email is required' },
            { rule: 'email', errorMessage: 'Enter a valid email' },
            { rule: 'maxLength', value: 50 }
        ],


        role: [
            { rule: 'required', errorMessage: 'Role is required' },
        ],


        name: [
            { rule: 'required', errorMessage: 'Name is required' },
            { rule: 'minLength', value: 3, errorMessage: 'Minimum 3 characters required' },
            { rule: 'maxLength', value: 30, errorMessage: 'Maximum 30 characters allowed' }
        ],

        phone: [
            { rule: 'required', errorMessage: 'Phone is required' },
            { rule: 'customRegexp', value: /^[0-9]{10,15}$/, errorMessage: 'Phone must be 10–15 digits' }
        ],


        password: [
            { rule: 'required', errorMessage: 'Password is required' },
            { rule: 'minLength', value: 6, errorMessage: 'Minimum 6 characters' },
            { rule: 'customRegexp', value: /[a-z]/, errorMessage: 'Must contain lowercase' },
            { rule: 'customRegexp', value: /[A-Z]/, errorMessage: 'Must contain uppercase' },
            { rule: 'customRegexp', value: /[0-9]/, errorMessage: 'Must contain number' },
            { rule: 'customRegexp', value: /[!@#$%^&*(),.?":{}|<>]/, errorMessage: 'Must contain special char' },
            { rule: 'customRegexp', value: /^\S+$/, errorMessage: 'No spaces allowed' }
        ],

        password_confirmation: [
            { rule: 'required', errorMessage: 'Confirm password required' },
            { rule: 'custom', validator: value => value === document.querySelector('[name="password"]')?.value, errorMessage: 'Passwords must match' }
        ],

        category_id: [{ rule: 'required', errorMessage: 'Category required' }],
        sub_category_id: [{ rule: 'required', errorMessage: 'Sub category required' }],
        metal_id: [{ rule: 'required', errorMessage: 'Metal required' }],
        status: [{ rule: 'required', errorMessage: 'Status required' }],

        product_name: [
            { rule: 'required', errorMessage: 'Product name required' },
            { rule: 'minLength', value: 3 },
            { rule: 'maxLength', value: 150 }
        ],

        title: [
            { rule: 'required', errorMessage: 'Title name required' },
            { rule: 'minLength', value: 3 },
            { rule: 'maxLength', value: 250 }
        ],



        content: [
            { rule: 'required', errorMessage: 'Content is required' },
            { rule: 'minLength', value: 3 },
        ],

        slug: [
            { rule: 'required', errorMessage: 'Slug required' },
            { rule: 'customRegexp', value: /^[a-z0-9]+(?:-[a-z0-9]+)*$/ },
            { rule: 'maxLength', value: 180 }
        ],

        sku: [
            { rule: 'required', errorMessage: 'SKU required' },
            { rule: 'minLength', value: 3 },
            { rule: 'maxLength', value: 50 }
        ],

        main_image: [
            { rule: 'required', errorMessage: 'Main image required' },
            { rule: 'files', value: { files: { maxSize: 5 * 1024 * 1024, extensions: ['jpg', 'jpeg', 'png', 'webp'] } }, errorMessage: 'Max 5MB JPG/PNG/WebP' }
        ],

        secondary_image: [
            { rule: 'required', errorMessage: 'Image image required' },
            { rule: 'files', value: { files: { maxSize: 5 * 1024 * 1024, extensions: ['jpg', 'jpeg', 'png', 'webp'] } }, errorMessage: 'Max 5MB JPG/PNG/WebP' }
        ],


        short_description: [
            { rule: 'required', errorMessage: 'Short description is required' },
            { rule: 'minLength', value: 80, errorMessage: 'Minimum 80 characters required' },
            { rule: 'maxLength', value: 100000, errorMessage: 'Maximum 100000 characters allowed' }
        ],


        description: [
            { rule: 'required', errorMessage: 'Product description is required' },
            { rule: 'minLength', value: 100, errorMessage: 'Minimum 100 characters required' },
            { rule: 'maxLength', value: 500000, errorMessage: 'Maximum 500000 characters allowed' }
        ],


        seo_title: [{ rule: 'maxLength', value: 60 }],
        seo_description: [{ rule: 'maxLength', value: 160 }],
        seo_keywords: [{ rule: 'maxLength', value: 255 }]
    };
    // ---------- VALIDATION + AJAX FUNCTION ----------
    window.initializeValidator = function () {

        document.querySelectorAll('.validate_form').forEach((form) => {

            // Prevent duplicate initialization
            if (form.dataset.validationInitialized) return;
            form.dataset.validationInitialized = "true";

            const validator = new JustValidate(form, {
                errorFieldCssClass: 'is-invalid',
                successFieldCssClass: 'is-valid',
            });

            // ADD RULES
            form.querySelectorAll('[name]').forEach((input) => {
                const r = RULES[input.name];
                if (r) validator.addField(`[name="${input.name}"]`, r);
            });

            // AJAX SUBMIT
            validator.onSuccess(async function () {

                const action = form.action;
                const method = form.method || "POST";
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const submitBtn = form.querySelector('[type="submit"]');
                let $submitBtn = submitBtn ? $(submitBtn) : null;

                if ($submitBtn) {
                    submitBtn.disabled = true;
                    startButtonLoader($submitBtn);
                }

                try {
                    const response = await fetch(action, {
                        method,
                        body: formData,
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": csrfToken
                        }

                    });

                    const data = await response.json().catch(() => ({}));

                    if (data.status === true) {

                        successToast(data.message || "Action successful!");

                        // Hide modal if open (for dynamic forms)
                        const modal = $('#app_gloval_modal');
                        if (modal.hasClass('show')) {
                            modal.modal('hide');
                            modal.find('.modal-content').html('');
                        }


                        if (typeof $.fn.DataTable !== 'undefined' && $.fn.DataTable.isDataTable('#dataTable')) {
                            let table = $('#dataTable').DataTable();
                            table.ajax.reload(null, false);
                        } else {
                            console.log('DataTable is not initialized yet.');
                        }




                    } else {
                        errorToast(data.message || "Something went wrong!");
                    }

                    // Stop loader safely
                    if ($submitBtn) {
                        stopButtonLoader($submitBtn);
                    }

                    // Redirect if URL present
                    if (data.redirect_url && data.redirect_url.trim() !== "") {
                        window.location.href = data.redirect_url;
                    }

                } catch (err) {
                    console.log("AJAX Error:", err);
                    errorToast("Failed to submit form! Please try again.");
                } finally {

                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }

                }
            });

        });
    };


    // ---------- INITIAL VALIDATION FOR NORMAL PAGE ----------
    initializeValidator();
});

// ---------- WHEN MODAL OPENS (Dynamic Forms) ----------
$(document).on('shown.bs.modal', '#app_gloval_modal', function () {
    initializeValidator();
});

// ---------- WHEN MODAL CLOSES (Remove old states) ----------
$(document).on('hidden.bs.modal', '#app_gloval_modal', function () {
    $('.validate_form .is-valid, .validate_form .is-invalid').removeClass('is-valid is-invalid');
});
