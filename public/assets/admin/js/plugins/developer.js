$(document).ready(function () {

    /* ============================================================
     * CONFIGURATION & INITIALIZATION
     * ============================================================ */
    const CONFIG = {
        CSRF_TOKEN: $('meta[name="csrf-token"]').attr("content"),
        AJAX_TIMEOUT: 20000,
        DEBOUNCE_DELAY: 300,
        NOTIFICATION_POLL_INTERVAL: 30000,
        MIN_SEARCH_LENGTH: 2
    };

    $.ajaxSetup({
        cache: false,
        timeout: CONFIG.AJAX_TIMEOUT,
        headers: { "X-CSRF-TOKEN": CONFIG.CSRF_TOKEN }
    });

    /* ============================================================
     * UTILITY FUNCTIONS
     * ============================================================ */

    /**
     * Button loader with proper state management
     */
    function startButtonLoader(btn) {
        const $btn = $(btn);
        if ($btn.prop("disabled")) return false;

        $btn.prop("disabled", true)
            .addClass("btn-disabled")
            .data("original-text", $btn.html())
            .data("loading", true)
            .html('<span class="spinner-border spinner-border-sm me-1"></span><span class="text">Processing...</span>');

        return true;
    }

    function stopButtonLoader(btn) {
        const $btn = $(btn);
        const originalText = $btn.data("original-text");

        if (originalText) {
            $btn.html(originalText);
        }

        $btn.prop("disabled", false)
            .removeClass("btn-disabled")
            .data("loading", false);
    }

    /**
     * Safe HTML escape
     */
    function escapeHtml(text = '') {
        return $('<div>').text(String(text)).html();
    }

    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /* ============================================================
     * TOAST NOTIFICATIONS
     * ============================================================ */
    function showToast(title, message, type = 'info') {
        // Ensure toast functions exist
        if (typeof successToast === 'function' && type === 'success') {
            successToast(message);
        } else if (typeof errorToast === 'function' && type === 'error') {
            errorToast(message);
        } else if (typeof window.toastr !== 'undefined') {
            toastr[type](message, title);
        } else {
            console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
        }
    }

    /* ============================================================
     * 1️⃣ UNIVERSAL AJAX BUTTON
     * ============================================================ */
    $(document).on("click", ".click_ajax_button", function (e) {
        e.preventDefault();

        const $btn = $(this);

        // Prevent double-click
        if ($btn.data("loading")) return;

        const url = $btn.data("url");
        const method = $btn.data("method") || "POST";
        const redirect = $btn.data("redirect") || null;
        const confirmMsg = $btn.data("confirm") || null;

        if (!url) {
            console.error("click_ajax_button: URL missing");
            return;
        }

        if (confirmMsg && !confirm(confirmMsg)) return;

        if (!startButtonLoader($btn)) return;

        $.ajax({
            url: url,
            type: method,
            success: function (res) {
                if (res.status) {
                    showToast('Success', res.message, 'success');
                } else {
                    showToast('Error', res.message, 'error');
                }

                const go = res.redirect_url || redirect;
                if (go) {
                    setTimeout(() => window.location.href = go, 300);
                }
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.message || "Request failed!";
                showToast('Error', errorMsg, 'error');
                console.error("AJAX ERROR:", xhr.responseText);
            },
            complete: function () {
                stopButtonLoader($btn);
            }
        });
    });

    /* ============================================================
     * 2️⃣ NORMAL REDIRECT BUTTON
     * ============================================================ */
    $(document).on("click", ".normal-call", function (e) {
        e.preventDefault();

        const url = $(this).attr("href"); // 👈 href se URL

        if (url) {
            window.location.href = url;
        } else {
            console.error("URL missing");
        }
    });



    $(document).on("click", ".modal_open", function (e) {
        e.preventDefault();

        const url = this.getAttribute("href");
        const $modal = $("#app_gloval_modal");
        const $content = $modal.find(".modal_content");

        if (!url) return console.error("URL missing");

        $content.html(`
        <div class="text-center p-3">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted">Loading...</p>
        </div>
    `);

        $modal.modal("show");

        $.get(url)
            .done((html, _, xhr) => {
                if (
                    xhr.status !== 200 ||
                    /Exception|Error|Whoops/.test(html)
                ) {
                    renderError(xhr, html);
                } else {
                    $content.html(html);
                }
            })
            .fail(xhr => renderError(xhr));

        function renderError(xhr, html = null) {
            const data =
                html ||
                xhr.responseText ||
                JSON.stringify(xhr.responseJSON || {}, null, 2) ||
                "No response";

            $content.html(`
            <div class="alert alert-danger m-3">
                <strong>Error ${xhr.status || 500}</strong>
                <pre class="mt-2 bg-light p-2 border rounded"
                     style="max-height:300px; overflow:auto; white-space:pre-wrap;">
${data.replace(/[&<>"']/g, m =>
                ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m])
            )}
                </pre>
            </div>
        `);
        }
    });





    // /* ============================================================
    //  * 3️⃣ AJAX MODAL OPEN
    //  * ============================================================ */
    // $(document).on("click", ".modal_open", function (event) {
    //     event.preventDefault();

    //     const $btn = $(this);
    //     const url = $btn.attr("href");
    //     const $modal = $("#app_gloval_modal");
    //     const $content = $modal.find(".modal_content");

    //     if (!url) {
    //         console.error("modal_open: URL missing");
    //         return;
    //     }

    //     $content.html(`
    //         <div class="p-3 text-center">
    //             <div class="spinner-border text-primary" role="status">
    //                 <span class="visually-hidden">Loading...</span>
    //             </div>
    //             <p class="mt-2 text-muted">Loading content...</p>
    //         </div>
    //     `);

    //     $modal.modal("show");

    //     $.ajax({
    //         url: url,
    //         type: "GET",
    //         dataType: "html",
    //         success: function (html) {
    //             $content.html(html);
    //         },
    //         error: function (xhr) {
    //             const errorHtml = `
    //                 <div class="p-4">
    //                     <div class="alert alert-danger">
    //                         <h5 class="alert-heading mb-2">
    //                             <i class="fas fa-exclamation-triangle me-2"></i>Failed to load content
    //                         </h5>
    //                         <p class="mb-2"><strong>Status:</strong> ${xhr.status} - ${xhr.statusText}</p>
    //                         <hr>
    //                         <pre class="bg-light p-3 border rounded" style="white-space:pre-wrap; max-height:300px; overflow:auto;">${escapeHtml(xhr.responseText)}</pre>
    //                     </div>
    //                 </div>`;
    //             $content.html(errorHtml);
    //         }
    //     });
    // });

    /* ============================================================
     * 4️⃣ GLOBAL STATUS CHANGE WITH ROLLBACK
     * ============================================================ */
    $(document).on('change', '.change-status', function () {
        const $dropdown = $(this);
        const newStatus = $dropdown.val();
        const previousStatus = $dropdown.data('previous-status') || $dropdown.find('option:first').val();
        const id = $dropdown.data('id');
        const url = $dropdown.data('url');
        const method = $dropdown.data('method') || 'POST';

        if (!url) {
            console.error('change-status: URL missing');
            return;
        }

        // Prevent multiple simultaneous requests
        if ($dropdown.prop('disabled')) return;

        $dropdown.prop('disabled', true).css('opacity', '0.6');

        $.ajax({
            url: url,
            method: method,
            data: {
                _token: CONFIG.CSRF_TOKEN,
                user_id: id,
                status: newStatus
            },
            success: function (res) {
                if (res.status) {
                    showToast('Success', res.message, 'success');
                    $dropdown.data('previous-status', newStatus);
                } else {
                    showToast('Error', res.message, 'error');
                    $dropdown.val(previousStatus);
                }
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Something went wrong';
                showToast('Error', errorMsg, 'error');
                $dropdown.val(previousStatus);
            },
            complete: function () {
                $dropdown.prop('disabled', false).css('opacity', '1');
            }
        });
    });

    /* ============================================================
     * 5️⃣ IMAGE PREVIEW POPUP
     * ============================================================ */
    $(document).on('click', '.show_image', function (e) {
        e.preventDefault();
        const imageUrl = $(this).attr('href');

        if (!imageUrl) return;

        $.confirm({
            title: 'Image Preview',
            theme: 'modern',
            columnClass: 'medium',
            type: 'blue',
            closeIcon: true,
            content: `
                <div class="text-center">
                    <img src="${escapeHtml(imageUrl)}" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-width:100%; max-height:500px; margin-bottom:20px;"
                         alt="Image preview">
                </div>
            `,
            buttons: {
                view: {
                    text: '<i class="fas fa-external-link-alt me-1"></i> View Full Image',
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

    /* ============================================================
     * 6️⃣ IMAGE DELETE WITH CONFIRMATION
     * ============================================================ */
    $(document).on('click', '.image_delete_btn', function () {
        const imageUrl = $(this).data('url');
        const $wrapper = $(this).closest('.image-wrapper');

        if (!imageUrl) {
            console.error('image_delete_btn: URL missing');
            return;
        }

        $.confirm({
            title: 'Delete Image',
            content: 'Are you sure you want to delete this image? This action cannot be undone.',
            type: 'red',
            theme: 'modern',
            buttons: {
                confirm: {
                    text: 'Yes, Delete',
                    btnClass: 'btn-red',
                    action: function () {
                        $.ajax({
                            url: imageUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
                            },
                            beforeSend: function () {
                                $wrapper.css('opacity', '0.5');
                            },
                            success: function (res) {
                                showToast('Success', res.message, 'success');
                                $wrapper.fadeOut(300, function () {
                                    $(this).remove();
                                });
                            },
                            error: function (xhr) {
                                const errorMsg = xhr.responseJSON?.message || 'Delete failed';
                                showToast('Error', errorMsg, 'error');
                                $wrapper.css('opacity', '1');
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

    /* ============================================================
     * 7️⃣ DELETE RECORD WITH SMOOTH ANIMATION
     * ============================================================ */
    $(document).on("click", ".delete_record", function (e) {
        e.preventDefault();

        const $btn = $(this);

        if ($btn.prop('disabled') || $btn.data('loading')) return;

        const url = $btn.attr("href");
        const redirect = $btn.data("redirect") || null;

        // Find target element to animate
        const $target = $btn.closest('tr').length ? $btn.closest('tr') :
            $btn.closest('.card').length ? $btn.closest('.card') :
                $btn.closest('.item').length ? $btn.closest('.item') :
                    $btn;

        if (!url) {
            console.error("delete_record: URL missing");
            return;
        }

        $.confirm({
            title: 'Delete Confirmation',
            content: 'Are you sure you want to delete this record? This action cannot be undone.',
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
                        startButtonLoader($btn);

                        // Soft processing animation
                        $target.css({
                            opacity: 0.6,
                            transform: 'scale(0.98)',
                            transition: 'all 0.25s ease'
                        });

                        $.ajax({
                            url: url,
                            type: "DELETE",
                            success: function (res) {
                                if (res.status) {
                                    showToast('Success', res.message, 'success');

                                    // Smooth delete animation
                                    $target.animate({ opacity: 0 }, 250, function () {
                                        $(this).slideUp(200, function () {
                                            $(this).remove();
                                        });
                                    });

                                    // Reload DataTable if exists
                                    if (typeof $.fn.DataTable !== 'undefined' &&
                                        $.fn.DataTable.isDataTable('#dataTable')) {
                                        $('#dataTable').DataTable().ajax.reload(null, false);
                                    }

                                    const go = res.redirect_url || redirect;
                                    if (go) {
                                        setTimeout(() => window.location.href = go, 450);
                                    }
                                } else {
                                    showToast('Error', res.message || 'Delete failed', 'error');
                                    rollback();
                                }
                            },
                            error: function (xhr) {
                                console.error(xhr);
                                const errorMsg = xhr.responseJSON?.message || "Delete request failed!";
                                showToast('Error', errorMsg, 'error');
                                rollback();
                            },
                            complete: function () {
                                stopButtonLoader($btn);
                            }
                        });

                        function rollback() {
                            $target.css({
                                opacity: 1,
                                transform: 'scale(1)'
                            });
                        }
                    }
                }
            }
        });
    });

    /* ============================================================
     * 8️⃣ NOTIFICATION SYSTEM
     * ============================================================ */
    const NotificationManager = (function () {
        const $notificationsList = $('#notificationsList');
        const $notificationBadge = $('#notificationBadge');
        let isFirstLoad = true;
        let isFetching = false;

        function fetchNotifications() {
            if (isFetching) return;
            isFetching = true;

            $.ajax({
                url: `/admin/notifications`,
                type: 'GET',
                success: function (res) {
                    if (!res.response) return;

                    const existingIds = $notificationsList.find('.notification-item')
                        .map(function () { return $(this).data('id'); })
                        .get();

                    const count = res.response.count || 0;
                    animateBadgeUpdate(count);

                    const newNotifications = [];
                    const currentNotificationIds = res.response.data.map(n => n.id);

                    res.response.data.forEach((notification, index) => {
                        if (!existingIds.includes(notification.id)) {
                            newNotifications.push({ notification, index });
                        } else {
                            updateNotificationReadStatus(notification.id, notification.read_at);
                        }
                    });

                    if (newNotifications.length > 0 && !isFirstLoad) {
                        addNotificationsWithAnimation(newNotifications);
                    } else if (isFirstLoad) {
                        addNotificationsInstantly(newNotifications);
                        isFirstLoad = false;
                    }

                    removeOldNotifications(existingIds, currentNotificationIds);
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

        function animateBadgeUpdate(count) {
            const currentCount = parseInt($notificationBadge.text()) || 0;

            if (count !== currentCount) {
                if (count > currentCount && count > 0) {
                    $notificationBadge.addClass('badge-pulse');
                    setTimeout(() => $notificationBadge.removeClass('badge-pulse'), 600);
                }

                $notificationBadge.css('transform', 'scale(1.2)');
                setTimeout(() => {
                    $notificationBadge.text(count);
                    $notificationBadge.css('transform', 'scale(1)');
                }, 150);
            }

            count > 0 ? $notificationBadge.fadeIn(200) : $notificationBadge.fadeOut(200);
        }

        function addNotificationsWithAnimation(notifications) {
            notifications.forEach(({ notification, index }) => {
                const $item = createNotificationElement(notification);
                $item.css({ opacity: 0, transform: 'translateX(30px)' })
                    .prependTo($notificationsList);

                setTimeout(() => {
                    $item.css({
                        transition: 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)',
                        opacity: 1,
                        transform: 'translateX(0)'
                    });
                }, index * 100);
            });
        }

        function addNotificationsInstantly(notifications) {
            notifications.forEach(({ notification }) => {
                createNotificationElement(notification).appendTo($notificationsList);
            });
        }

        function createNotificationElement(notification) {
            const isUnread = !notification.read_at;
            const unreadClass = isUnread ? 'notification-unread' : '';
            const boldClass = isUnread ? 'fw-bold' : '';
            const notificationUrl = notification.url || '#';

            return $(`
                <li class="dropdown-notifications-list-item ${unreadClass}" style="transition: all 0.3s ease;">
                    <div class="notification-wrapper">
                        <a href="${escapeHtml(notificationUrl)}" 
                           class="notification-item ${boldClass}" 
                           data-id="${notification.id}"
                           data-url="${escapeHtml(notificationUrl)}">
                            <div class="notification-content">
                                <div class="notification-header">
                                    <div class="notification-icon ${getNotificationIconColor(notification.type)}">
                                        <i class="${getNotificationIcon(notification.type)}"></i>
                                    </div>
                                    <div class="notification-text flex-grow-1">
                                        <h6 class="notification-title mb-1">${escapeHtml(notification.title)}</h6>
                                        <p class="notification-message mb-1">${escapeHtml(notification.message)}</p>
                                        <small class="notification-time text-muted">
                                            <i class="ri-time-line"></i> ${escapeHtml(notification.created_at)}
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

        function updateNotificationReadStatus(id, readAt) {
            const $item = $notificationsList.find(`.notification-item[data-id="${id}"]`);
            const $listItem = $item.closest('li');

            if (readAt) {
                $item.removeClass('fw-bold');
                $listItem.removeClass('notification-unread');
                $item.find('.notification-dot').fadeOut(200, function () {
                    $(this).remove();
                });
            }
        }

        function removeOldNotifications(existingIds, currentIds) {
            $notificationsList.find('.notification-item').each(function () {
                const id = $(this).data('id');
                if (!currentIds.includes(id)) {
                    const $item = $(this).closest('li');
                    $item.css({ opacity: 1, transform: 'translateX(0)' })
                        .animate({ opacity: 0 }, 300)
                        .css({ transform: 'translateX(-30px)', transition: 'transform 0.3s ease' });

                    setTimeout(() => {
                        $item.slideUp(200, function () { $(this).remove(); });
                    }, 300);
                }
            });
        }

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
                    `).appendTo($notificationsList);

                    setTimeout(() => $empty.css({ opacity: 1, transition: 'opacity 0.3s ease' }), 50);
                }
            } else {
                $emptyState.fadeOut(200, function () { $(this).remove(); });
            }
        }

        return {
            init: fetchNotifications,
            fetch: fetchNotifications
        };
    })();

    // Mark single notification as read
    $(document).on('click', '.notification-item', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $listItem = $this.closest('li');
        const id = $this.data('id');
        const url = $this.attr('href') || $this.data('url');

        const isValidUrl = url && url !== '#' && url !== 'javascript:void(0);' &&
            url !== 'null' && url.trim() !== '';

        if (!$listItem.hasClass('notification-unread')) {
            if (isValidUrl) window.location.href = url;
            return false;
        }

        $listItem.css({
            backgroundColor: '#f0f7ff',
            transition: 'background-color 0.3s ease'
        });

        setTimeout(() => $listItem.css('backgroundColor', ''), 300);

        $.ajax({
            url: `/admin/notifications/read/${id}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: { _token: CONFIG.CSRF_TOKEN },
            dataType: 'json',
            beforeSend: function () {
                $this.css('opacity', '0.6');
            },
            success: function (response) {
                $this.removeClass('fw-bold').css('opacity', '1');
                $listItem.removeClass('notification-unread');

                const $dot = $this.find('.notification-dot');
                if ($dot.length) {
                    $dot.css({ transform: 'scale(0)', transition: 'transform 0.2s ease' });
                    setTimeout(() => $dot.remove(), 200);
                }

                const currentCount = parseInt($('#notificationBadge').text()) || 0;
                if (currentCount > 0) {
                    $('#notificationBadge').text(currentCount - 1);
                }

                if (isValidUrl) {
                    setTimeout(() => window.location.href = url, 400);
                }
            },
            error: function (xhr, status, error) {
                $this.css('opacity', '1');
                console.error('Failed to mark notification as read:', error);

                if (isValidUrl) {
                    setTimeout(() => window.location.href = url, 500);
                }
            }
        });

        return false;
    });

    // Mark all notifications as read
    $('#markAllRead').click(function (e) {
        e.preventDefault();

        const $button = $(this);
        const originalText = $button.text();
        const $unreadItems = $('#notificationsList').find('.notification-unread');

        if ($unreadItems.length === 0) {
            showToast('Info', 'No unread notifications', 'info');
            return;
        }

        $button.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1"></span>Marking...');

        $.ajax({
            url: `/admin/notifications/read-all`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: { _token: CONFIG.CSRF_TOKEN },
            dataType: 'json',
            success: function (response) {
                $unreadItems.each(function (index) {
                    const $item = $(this);
                    setTimeout(() => {
                        $item.css({
                            backgroundColor: '#f0f7ff',
                            transition: 'background-color 0.3s ease'
                        });

                        setTimeout(() => {
                            $item.css('backgroundColor', '').removeClass('notification-unread');
                            $item.find('.notification-item').removeClass('fw-bold');

                            const $dot = $item.find('.notification-dot');
                            if ($dot.length) {
                                $dot.css({ transform: 'scale(0)', transition: 'transform 0.2s ease' });
                                setTimeout(() => $dot.remove(), 200);
                            }
                        }, 300);
                    }, index * 80);
                });

                setTimeout(() => {
                    $button.prop('disabled', false).text(originalText);
                    $('#notificationBadge').text(0).fadeOut(200);
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

    // Initialize notifications
    if ($('#notificationsList').length) {
        NotificationManager.init();
        // Uncomment to enable polling
        // setInterval(NotificationManager.fetch, CONFIG.NOTIFICATION_POLL_INTERVAL);
    }




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
