$(document).ready(function () {

    const CSRF = $('meta[name="csrf-token"]').attr("content");

    $.ajaxSetup({
        cache: false,
        timeout: 20000,
        headers: { "X-CSRF-TOKEN": CSRF }
    });

    /* ============================================================
     * UNIVERSAL BUTTON LOADER (disable + class + Processing text)
     * ============================================================ */
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


    /* ============================================================
     * 1️⃣ UNIVERSAL AJAX BUTTON
     * ============================================================ */
    $(document).on("click", ".click_ajax_button", function (e) {
        e.preventDefault();

        const btn = $(this);
        const url = btn.data("url");
        const method = btn.data("method") || "POST";
        const redirect = btn.data("redirect") || null;
        const confirmMsg = btn.data("confirm") || null;

        if (!url) return console.error("click_ajax_button: URL missing");
        if (confirmMsg && !confirm(confirmMsg)) return;

        startButtonLoader(btn);

        $.ajax({
            url: url,
            type: method,
            success: function (res) {
                if (res.status) successToast(res.message);
                else errorToast(res.message);

                const go = res.redirect_url || redirect;
                if (go) setTimeout(() => window.location.href = go, 300);
            },
            error: function (xhr) {
                errorToast("Request failed!");
                console.error("AJAX ERROR:", xhr.responseText);
            },
            complete: function () {
                stopButtonLoader(btn);
            }
        });
    });


    /* ============================================================
     * 2️⃣ NORMAL REDIRECT BUTTON
     * ============================================================ */
    $(document).on("click", ".normal-call", function () {
        const url = $(this).data("url");
        if (url) window.location.href = url;
        else console.error("normal-call: URL missing");
    });


    /* ============================================================
     * 3️⃣ AJAX MODAL OPEN
     * ============================================================ */
    $(document).on("click", ".modal_open", function (event) {
        event.preventDefault();

        const btn = $(this);
        const url = btn.attr("href");
        const $modal = $("#app_gloval_modal");
        const $content = $modal.find(".modal_content");

        if (!url) return console.error("modal_open: URL missing");

        $content.html('<div class="p-3 text-center">Loading...</div>');
        $modal.modal("show");

        $.ajax({
            url: url,
            type: "GET",
            dataType: "html",

            success: function (html) {
                $content.html(html);
            },
            error: function (xhr) {
                let errorHtml = `
                <div class="p-3">
                    <h5 class="text-danger mb-2">Failed to load content</h5>
                    <p><strong>Status:</strong> ${xhr.status} - ${xhr.statusText}</p>
                    <pre class="bg-light p-2 border rounded" style="white-space:pre-wrap; max-height:300px; overflow:auto;">
                         ${xhr.responseText}
                    </pre>
                </div>`;
                $content.html(errorHtml);
            }
        });
    });


    /* ============================================================
     * 4️⃣ GLOBAL STATUS CHANGE
     * ============================================================ */
    // $(document).on('change', '.change-status', function () {
    //     let dropdown = $(this);
    //     let status = dropdown.val();


    //     let id = dropdown.data('id');
    //     let url = dropdown.data('url');
    //     let method = dropdown.data('method');

    //     if (!url) return console.error('change-status: URL missing');

    //     dropdown.prop('disabled', true);

    //     $.ajax({
    //         url: url,
    //         method: method,
    //         data: {
    //             _token: CSRF,
    //             user_id: id,
    //             status: status
    //         },
    //         success: function (res) {
    //             if (res.status) successToast(res.message);
    //             else errorToast(res.message);
    //         },
    //         error: function (xhr) {
    //             errorToast(xhr.responseJSON?.message || 'Something went wrong');
    //         },
    //         complete: function () {
    //             dropdown.prop('disabled', false);
    //         }
    //     });
    // });



    $(document).on('change', '.change-status', function () {
        let dropdown = $(this);
        let status = dropdown.val();
        let previousStatus = dropdown.data('previous-status') || dropdown.find('option:first').val();

        let id = dropdown.data('id');
        let url = dropdown.data('url');
        let method = dropdown.data('method');

        if (!url) return console.error('change-status: URL missing');

        // Store current selection as previous for next change
        dropdown.data('previous-status', previousStatus);
        dropdown.prop('disabled', true);

        $.ajax({
            url: url,
            method: method,
            data: {
                _token: CSRF,
                user_id: id,
                status: status
            },
            success: function (res) {
                if (res.status) {
                    successToast(res.message);
                    dropdown.data('previous-status', status);
                } else {
                    errorToast(res.message);
                    dropdown.val(previousStatus);
                }
            },
            error: function (xhr) {
                errorToast(xhr.responseJSON?.message || 'Something went wrong');
                // Revert to previous status on error
                dropdown.val(previousStatus);
            },
            complete: function () {
                dropdown.prop('disabled', false);
            }
        });
    });


    /* -------------------------------------
             * Image Preview Popup
             * ------------------------------------- */
    $(document).on('click', '.show_image', function (e) {
        e.preventDefault();
        let imageUrl = $(this).attr('href');
        $.confirm({
            title: 'Image Preview',
            theme: 'modern',
            columnClass: 'medium',
            type: 'blue',
            closeIcon: true,
            content: `
                <div class="text-center">
                    <img src="${imageUrl}" style="max-width:100%; border-radius:8px; margin-bottom:20px;">
                </div>
            `,
            buttons: {
                view: {
                    text: 'View Full Image',
                    btnClass: 'btn-blue',
                    action: function () {
                        window.open(imageUrl, '_blank');
                    }
                },
                close: {
                    text: 'Close',
                    btnClass: 'btn-secondary'
                }
            }
        });
    });





    $(document).on('click', '.image_delete_btn', function () {
        let imageUrl = $(this).data('url');
        let wrapper = $(this).closest('.image-wrapper');
        $.confirm({
            title: 'Delete Image',
            content: 'Are you sure you want to delete this image? This action cannot be undone.',
            type: 'red',
            buttons: {
                confirm: {
                    text: 'Yes, Delete',
                    btnClass: 'btn-red',
                    action: function () {
                        $.ajax({
                            url: imageUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            beforeSend: function () {
                                wrapper.css('opacity', '0.5');
                            },
                            success: function (res) {
                                successToast(res.message);
                                wrapper.fadeOut(300, function () {
                                    $(this).remove();
                                });
                            },
                            error: function (xhr) {
                                errorToast(xhr.responseJSON?.message || 'Delete failed');
                                wrapper.css('opacity', '1');
                            }
                        });
                    }
                },
                cancel: {
                    text: 'Cancel'
                }
            }
        });
    });


    $(document).on("click", ".delete_record", function (e) {
        e.preventDefault();

        const btn = $(this);
        if (btn.prop('disabled')) return;

        const url = btn.attr("href");
        const redirect = btn.data("redirect") || null;

        // Target element to animate (row / card / wrapper)
        const target =
            btn.closest('tr').length ? btn.closest('tr') :
                btn.closest('.card').length ? btn.closest('.card') :
                    btn.closest('.item').length ? btn.closest('.item') :
                        btn;

        if (!url) return console.error("delete_record: URL missing");

        $.confirm({
            title: 'Delete Confirmation',
            content: 'Are you sure you want to delete this record? This action cannot be undone. ',
            type: 'red',
            theme: 'modern',
            animation: 'scale',
            closeAnimation: 'scale',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-secondary'
                },
                confirm: {
                    text: 'Yes, Delete',
                    btnClass: 'btn-danger',
                    action: function () {

                        // Button loader
                        startButtonLoader(btn);

                        // Soft "processing" animation
                        target.css({
                            opacity: 0.6,
                            transform: 'scale(0.98)',
                            transition: 'all 0.25s ease'
                        });

                        $.ajax({
                            url: url,
                            type: "DELETE",

                            success: function (res) {
                                if (res.status) {
                                    successToast(res.message);

                                    // Smooth delete animation
                                    target.animate(
                                        { opacity: 0 },
                                        250,
                                        function () {
                                            $(this).slideUp(200, function () {
                                                $(this).remove();
                                            });
                                        }
                                    );

                                    // DataTable reload
                                    if (
                                        typeof $.fn.DataTable !== 'undefined' &&
                                        $.fn.DataTable.isDataTable('#dataTable')
                                    ) {
                                        $('#dataTable').DataTable().ajax.reload(null, false);
                                    }

                                    const go = res.redirect_url || redirect;
                                    if (go) {
                                        setTimeout(() => window.location.href = go, 450);
                                    }

                                } else {
                                    errorToast(res.message || 'Delete failed');
                                    rollback();
                                }
                            },

                            error: function (xhr) {
                                console.error(xhr);
                                errorToast(xhr.responseJSON?.message || "Delete request failed!");
                                rollback();
                            },

                            complete: function () {
                                stopButtonLoader(btn);
                            }
                        });

                        function rollback() {
                            target.css({
                                opacity: 1,
                                transform: 'scale(1)'
                            });
                        }
                    }
                }
            }
        });
    });


    // =========================================

    // =======================================
    // PROFESSIONAL NOTIFICATION SYSTEM - NO PAGE RELOAD
    // =======================================

    const notificationsList = $('#notificationsList');
    const notificationBadge = $('#notificationBadge');
    let isFirstLoad = true;
    let isFetching = false;

    // Enhanced fetch notifications with smooth animations
    function fetchNotifications() {
        if (isFetching) return;
        isFetching = true;

        $.ajax({
            url: `/admin/notifications`,
            type: 'GET',
            success: function (res) {
                const existingIds = notificationsList.find('.notification-item').map(function () {
                    return $(this).data('id');
                }).get();

                const count = res.response.count || 0;

                // Animate badge update
                animateBadgeUpdate(count);

                // Process notifications
                const newNotifications = [];
                const currentNotificationIds = res.response.data.map(n => n.id);

                res.response.data.forEach((notification, index) => {
                    const id = notification.id;

                    if (!existingIds.includes(id)) {
                        newNotifications.push({ notification, index });
                    } else {
                        // Update existing notification if read status changed
                        updateNotificationReadStatus(id, notification.read_at);
                    }
                });

                // Add new notifications with staggered animation
                if (newNotifications.length > 0 && !isFirstLoad) {
                    addNotificationsWithAnimation(newNotifications);
                } else if (isFirstLoad) {
                    addNotificationsInstantly(newNotifications);
                    isFirstLoad = false;
                }

                // Remove notifications that no longer exist with fade out
                removeOldNotifications(existingIds, currentNotificationIds);

                // Show empty state if no notifications
                toggleEmptyState(res.response.data.length === 0);
            },
            error: function () {
                console.error('Failed to fetch notifications');
            },
            complete: function () {
                isFetching = false;
            }
        });
    }

    // Animate badge count update
    function animateBadgeUpdate(count) {
        const currentCount = parseInt(notificationBadge.text()) || 0;

        if (count !== currentCount) {
            if (count > currentCount && count > 0) {
                // Pulse animation for new notifications
                notificationBadge.addClass('badge-pulse');
                setTimeout(() => notificationBadge.removeClass('badge-pulse'), 600);
            }

            // Animate number change
            notificationBadge.css('transform', 'scale(1.2)');
            setTimeout(() => {
                notificationBadge.text(count);
                notificationBadge.css('transform', 'scale(1)');
            }, 150);
        }

        count > 0 ? notificationBadge.fadeIn(200) : notificationBadge.fadeOut(200);
    }

    // Add notifications with staggered slide-in animation
    function addNotificationsWithAnimation(notifications) {
        notifications.forEach(({ notification, index }) => {
            const $item = createNotificationElement(notification);
            $item.css({
                opacity: 0,
                transform: 'translateX(30px)'
            }).prependTo(notificationsList);

            // Staggered animation
            setTimeout(() => {
                $item.css({
                    transition: 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)',
                    opacity: 1,
                    transform: 'translateX(0)'
                });
            }, index * 100);
        });
    }

    // Add notifications instantly (first load)
    function addNotificationsInstantly(notifications) {
        notifications.forEach(({ notification }) => {
            const $item = createNotificationElement(notification);
            $item.appendTo(notificationsList);
        });
    }

    // Create notification HTML element
    function createNotificationElement(notification) {
        const isUnread = !notification.read_at;
        const unreadClass = isUnread ? 'notification-unread' : '';
        const boldClass = isUnread ? 'fw-bold' : '';
        const notificationUrl = notification.url || '#';

        return $(`
        <li class="dropdown-notifications-list-item ${unreadClass}" style="transition: all 0.3s ease;">
            <div class="notification-wrapper">
                <a href="${notificationUrl}" 
                   class="notification-item ${boldClass}" 
                   data-id="${notification.id}"
                   data-url="${notificationUrl}">
                    <div class="notification-content">
                        <div class="notification-header">
                            <div class="notification-icon ${getNotificationIconColor(notification.type)}">
                                <i class="${getNotificationIcon(notification.type)}"></i>
                            </div>
                            <div class="notification-text flex-grow-1">
                                <h6 class="notification-title mb-1">${notification.title}</h6>
                                <p class="notification-message mb-1">${notification.message}</p>
                                <small class="notification-time text-muted">
                                    <i class="ri-time-line"></i> ${notification.created_at}
                                </small>
                            </div>
                            ${isUnread ? '<span class="notification-dot"></span>' : ''}
                        </div>
                    </div>
                </a>
            </div>
        </li>
    `);
    }

    // Get notification icon based on type
    function getNotificationIcon(type) {
        const icons = {
            'success': 'ri-checkbox-circle-line',
            'warning': 'ri-alert-line',
            'error': 'ri-error-warning-line',
            'info': 'ri-information-line',
            'message': 'ri-message-2-line',
            'default': 'ri-notification-3-line'
        };
        return icons[type] || icons['default'];
    }

    // Get notification icon color class
    function getNotificationIconColor(type) {
        const colors = {
            'success': 'icon-success',
            'warning': 'icon-warning',
            'error': 'icon-error',
            'info': 'icon-info',
            'message': 'icon-primary',
            'default': 'icon-default'
        };
        return colors[type] || colors['default'];
    }

    // Update notification read status
    function updateNotificationReadStatus(id, readAt) {
        const $item = notificationsList.find(`.notification-item[data-id="${id}"]`);
        const $listItem = $item.closest('li');

        if (readAt) {
            $item.removeClass('fw-bold');
            $listItem.removeClass('notification-unread');
            $item.find('.notification-dot').fadeOut(200, function () {
                $(this).remove();
            });
        }
    }

    // Remove old notifications with fade out
    function removeOldNotifications(existingIds, currentIds) {
        notificationsList.find('.notification-item').each(function () {
            const id = $(this).data('id');
            if (!currentIds.includes(id)) {
                $(this).closest('li').css({
                    opacity: 1,
                    transform: 'translateX(0)'
                }).animate({
                    opacity: 0
                }, 300).css({
                    transform: 'translateX(-30px)',
                    transition: 'transform 0.3s ease'
                });

                setTimeout(() => {
                    $(this).closest('li').slideUp(200, function () {
                        $(this).remove();
                    });
                }, 300);
            }
        });
    }

    // Toggle empty state
    function toggleEmptyState(isEmpty) {
        const emptyStateId = 'notificationsEmptyState';
        const $emptyState = $(`#${emptyStateId}`);

        if (isEmpty) {
            if ($emptyState.length === 0) {
                const $empty = $(`
                <li id="${emptyStateId}" class="text-center py-5" style="opacity: 0;">
                    <div class="empty-state-icon mb-3">
                        <i class="ri-notification-off-line" style="font-size: 3rem; color: #ccc;"></i>
                    </div>
                    <p class="text-muted mb-0">No notifications yet</p>
                    <small class="text-muted">We'll notify you when something new arrives</small>
                </li>
            `).appendTo(notificationsList);

                setTimeout(() => $empty.css({ opacity: 1, transition: 'opacity 0.3s ease' }), 50);
            }
        } else {
            $emptyState.fadeOut(200, function () {
                $(this).remove();
            });
        }
    }

    // Mark single notification as read - AJAX ONLY (NO PAGE RELOAD)
    $(document).on('click', '.notification-item', function (e) {
        e.preventDefault(); // Prevent default link behavior

        const $this = $(this);
        const $listItem = $this.closest('li');
        const id = $this.data('id');
        const url = $this.attr('href') || $this.data('url');

        // Check if URL is valid for navigation
        const isValidUrl = url && url !== '#' && url !== 'javascript:void(0);' && url !== 'null' && url.trim() !== '';

        // If already read, just navigate (only if valid URL)
        if (!$listItem.hasClass('notification-unread')) {
            if (isValidUrl) {
                window.location.href = url;
            }
            return false;
        }

        // Visual feedback - Ripple effect
        $listItem.css({
            backgroundColor: '#f0f7ff',
            transition: 'background-color 0.3s ease'
        });

        setTimeout(() => {
            $listItem.css('backgroundColor', '');
        }, 300);

        // Get CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Mark as read via AJAX
        $.ajax({
            url: `/admin/notifications/read/${id}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                _token: csrfToken
            },
            dataType: 'json',
            beforeSend: function () {
                // Add loading state to notification
                $this.css('opacity', '0.6');
            },
            success: function (response) {
                // Smooth transition to read state
                $this.removeClass('fw-bold').css('opacity', '1');
                $listItem.removeClass('notification-unread');

                // Remove unread dot with animation
                const $dot = $this.find('.notification-dot');
                if ($dot.length) {
                    $dot.css({
                        transform: 'scale(0)',
                        transition: 'transform 0.2s ease'
                    });
                    setTimeout(() => $dot.remove(), 200);
                }

                // Update badge count immediately
                const currentCount = parseInt(notificationBadge.text()) || 0;
                if (currentCount > 0) {
                    const newCount = currentCount - 1;
                    animateBadgeUpdate(newCount);
                }

                // Navigate only if URL is valid
                if (isValidUrl) {
                    setTimeout(() => {
                        window.location.href = url;
                    }, 400);
                } else {
                    // Just show success feedback without navigation
                    $listItem.css({
                        backgroundColor: '#e7f5e9',
                        transition: 'background-color 0.3s ease'
                    });
                    setTimeout(() => {
                        $listItem.css('backgroundColor', '');
                    }, 500);
                }
            },
            error: function (xhr, status, error) {
                // Reset opacity
                $this.css('opacity', '1');

                // Show error notification
                console.error('Failed to mark notification as read:', error);
                showToast('Error', 'Failed to mark notification as read', 'error');

                // Navigate only if valid URL, even if marking failed
                if (isValidUrl) {
                    setTimeout(() => {
                        window.location.href = url;
                    }, 500);
                }
            }
        });

        return false;
    });

    // Mark all notifications as read with cascade animation
    $('#markAllRead').click(function (e) {
        e.preventDefault();

        const $button = $(this);
        const originalText = $button.text();
        const $unreadItems = notificationsList.find('.notification-unread');

        // Check if there are unread notifications
        if ($unreadItems.length === 0) {
            showToast('Info', 'No unread notifications', 'info');
            return;
        }

        // Button loading state
        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Marking...');

        // Get CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: `/admin/notifications/read-all`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                _token: csrfToken
            },
            dataType: 'json',
            success: function (response) {
                // Cascade animation for all notifications
                $unreadItems.each(function (index) {
                    const $item = $(this);
                    setTimeout(() => {
                        // Ripple effect
                        $item.css({
                            backgroundColor: '#f0f7ff',
                            transition: 'background-color 0.3s ease'
                        });

                        setTimeout(() => {
                            $item.css('backgroundColor', '').removeClass('notification-unread');
                            $item.find('.notification-item').removeClass('fw-bold');

                            // Remove dot with scale animation
                            const $dot = $item.find('.notification-dot');
                            if ($dot.length) {
                                $dot.css({
                                    transform: 'scale(0)',
                                    transition: 'transform 0.2s ease'
                                });
                                setTimeout(() => $dot.remove(), 200);
                            }
                        }, 300);
                    }, index * 80);
                });

                // Reset button and update badge
                setTimeout(() => {
                    $button.prop('disabled', false).text(originalText);
                    animateBadgeUpdate(0);
                    showToast('Success', 'All notifications marked as read', 'success');
                }, ($unreadItems.length * 80) + 500);
            },
            error: function (xhr, status, error) {
                console.error('Failed to mark all as read:', error);
                $button.prop('disabled', false).text(originalText);
                showToast('Error', 'Failed to mark all notifications as read', 'error');
            }
        });
    });

    // Dropdown animation
    $(document).on('show.bs.dropdown', '.app-header-notification', function () {
        const $menu = $(this).find('.dropdown-menu');
        $menu.css({
            opacity: 0,
            transform: 'translateY(-10px)'
        });

        setTimeout(() => {
            $menu.css({
                opacity: 1,
                transform: 'translateY(0)',
                transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'
            });
        }, 10);
    });

    // Close dropdown when clicking on a notification (only if URL is valid)
    $(document).on('click', '.notification-item', function () {
        const url = $(this).attr('href') || $(this).data('url');
        const isValidUrl = url && url !== '#' && url !== 'javascript:void(0);' && url !== 'null' && url.trim() !== '';

        // Close the dropdown after a delay only if redirecting
        if (isValidUrl) {
            setTimeout(() => {
                $('.app-header-notification .dropdown-toggle').dropdown('hide');
            }, 500);
        }
    });

    // Toast notification function (optional - for better UX)
    function showToast(title, message, type = 'info') {
        // Check if you have a toast/notification library
        // Otherwise, use browser notification or simple alert

        // Example with browser notification API
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/default_images/logo.png'
            });
        } else {
            // Fallback to console
            console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
        }

        // You can integrate with libraries like:
        // - Toastr
        // - SweetAlert2
        // - Bootstrap Toast
        // Example: toastr[type](message, title);
    }

    // Request notification permission on page load (optional)
    $(document).ready(function () {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    });

    // Initial fetch & polling
    fetchNotifications();
    // setInterval(fetchNotifications, 30000);

    // Play notification sound for new notifications (optional)
    function playNotificationSound() {
        // Uncomment to enable sound
        // const audio = new Audio('/path/to/notification-sound.mp3');
        // audio.volume = 0.3;
        // audio.play().catch(e => console.log('Audio play failed:', e));
    }

    // Prevent page reload when clicking dropdown menu
    $(document).on('click', '.dropdown-menu', function (e) {
        if (!$(e.target).is('a')) {
            e.stopPropagation();
        }
    });




    $(document).on('change', '.image-upload-input', function () {
        let input = this;
        let uploadUrl = $(this).data('upload-url');

        if (!input.files.length || !uploadUrl) return;

        let formData = new FormData();

        $.each(input.files, function (i, file) {
            formData.append('images[]', file);
        });

        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                console.log(res);
                // yaha preview / reload images / toast laga sakte ho
            },
            error: function (err) {
                console.error(err);
                alert('Image upload failed');
            }
        });
    });





    // GLOVAL SEARCH JS=======================================================================================================================================


    $(function () {

        /* ===============================
         * Constants & Cached Elements
         * =============================== */
        const MIN_LENGTH = 2;
        const DEBOUNCE_MS = 300;
        const SEARCH_URL = '/admin/global-search';

        const $form = $('#globalSearchForm');
        const $input = $('#globalSearchInput');
        const $results = $('#resultsContainer');
        const $content = $('#searchContent');
        const $clearBtn = $('#clearBtn');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        let debounceTimer = null;
        let activeRequest = null;

        /* ===============================
         * Event Handlers
         * =============================== */

        $input.on('input', function () {
            const query = this.value.trim();
            $clearBtn.toggleClass('show', query.length > 0);

            clearTimeout(debounceTimer);

            if (query.length < MIN_LENGTH) {
                hideResults();
                return;
            }

            debounceTimer = setTimeout(() => performSearch(query), DEBOUNCE_MS);
        });

        $clearBtn.on('click', function () {
            $input.val('').focus();
            hideResults();
            $(this).removeClass('show');
        });

        $form.on('submit', function (e) {
            e.preventDefault();
            const query = $input.val().trim();
            if (query.length >= MIN_LENGTH) {
                performSearch(query);
            }
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.search-wrapper').length) {
                hideResults();
            }
        });

        /* ===============================
         * Core Search
         * =============================== */

        function performSearch(query) {
            abortPreviousRequest();
            showResults();
            showLoader();

            activeRequest = $.ajax({
                url: SEARCH_URL,
                method: 'GET',
                data: {
                    query
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: handleSuccess,
                error(xhr) {
                    if (xhr.statusText !== 'abort') {
                        renderMessage('fa-exclamation-circle',
                            'Something went wrong. Please try again.');
                    }
                }
            });
        }

        function handleSuccess(response) {
            if (!response?.success || !response.total) {
                renderMessage('fa-search', response?.message || 'No results found');
                return;
            }
            renderResults(response);
        }

        /* ===============================
         * Rendering
         * =============================== */

        function renderResults({
            results = {},
            total,
            query
        }) {
            let html = '';

            if (results.users?.length) {
                html += renderSection('Users', results.users, renderUser);
            }

            if (results.orders?.length) {
                html += renderSection('Orders', results.orders, renderOrder);
            }

            html += renderFooter(total, query);
            $content.html(html);
        }

        function renderSection(title, items, renderer) {
            return `
            <div class="section-header">${escapeHtml(title)}</div>
            ${items.map(renderer).join('')}
        `;
        }

        function renderUser(user) {
            return `
            <a href="${user.url}" class="result-item">
                <div class="result-content">
                    <div class="result-title">
                        <i class="fas fa-user"></i>
                        ${escapeHtml(user.title)}
                    </div>
                    <div class="result-subtitle">${escapeHtml(user.subtitle)}</div>
                </div>
            </a>
        `;
        }

        function renderOrder(order) {
            const statusClass = {
                pending: 'warning',
                processing: 'primary',
                completed: 'success',
                cancelled: 'danger'
            }[order.status] || 'secondary';

            return `
            <a href="${order.url}" class="result-item">
                <div class="result-main">
                    <div class="result-content">
                        <div class="result-title">
                            <i class="fas fa-shopping-cart"></i>
                            ${escapeHtml(order.title)}
                        </div>
                        <div class="result-subtitle">${escapeHtml(order.subtitle)}</div>
                    </div>
                    <div class="result-meta">
                        <span class="badge badge-${statusClass}">
                            ${escapeHtml(order.status)}
                        </span>
                        <span class="result-date">${escapeHtml(order.created_at)}</span>
                    </div>
                </div>
            </a>
        `;
        }

        function renderFooter(total, query) {
            return `
            <div class="footer">
                Showing <strong>${total}</strong> result${total !== 1 ? 's' : ''}
                for "<strong>${escapeHtml(query)}</strong>"
            </div>
        `;
        }

        /* ===============================
         * UI Helpers
         * =============================== */

        function showResults() {
            $results.addClass('show');
        }

        function hideResults() {
            $results.removeClass('show');
            $content.empty();
        }

        function showLoader() {
            $content.html(`
            <div class="loading-indicator">
                <div class="spinner"></div>
                <span>Searching...</span>
            </div>
        `);
        }

        function renderMessage(icon, message) {
            $content.html(`
            <div class="message">
                <i class="fas ${icon}"></i>
                <div class="message-text">${escapeHtml(message)}</div>
            </div>
        `);
        }

        function abortPreviousRequest() {
            if (activeRequest) {
                activeRequest.abort();
                activeRequest = null;
            }
        }

        function escapeHtml(text = '') {
            return $('<div>').text(text).html();
        }

    });



});
