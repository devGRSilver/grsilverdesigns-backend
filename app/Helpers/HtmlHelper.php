<?php

use App\Enums\OrderStatus;
use App\Enums\TransactionStatus;

function button_group($buttons = [])
{
    if (empty($buttons)) {
        return '';
    }

    $html = '<div class="btn-group" role="group" aria-label="Action Buttons">';
    foreach ($buttons as $btn) {
        $html .= $btn;
    }
    $html .= '</div>';
    return $html;
}

// Normal view button
function btn_view($url, $ajax = false)
{
    $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

    if ($ajax) {
        return '<button href="' . $escapedUrl . '" type="button" class="btn btn-success modal_open">View</button>';
    } else {
        return '<button onclick="window.location.href=\'' . $escapedUrl . '\'" type="button" class="btn btn-success normal-call">View</button>';
    }
}

function redirect_to_link($url, $title)
{
    $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    return '<a href="' . $escapedUrl . '" class="text-primary" target="_blank">' . $escapedTitle . '</a>';
}

// Normal edit button
function btn_edit($url, $ajax = false)
{
    $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    if ($ajax) {
        return '<button href="' . $escapedUrl . '" type="button" class="btn btn-warning modal_open">Edit</button>';
    } else {
        return '<button onclick="window.location.href=\'' . $escapedUrl . '\'" type="button" class="btn btn-warning normal-call">Edit</button>';
    }
}

// Delete button (usually AJAX)
function btn_delete($url)
{
    $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

    return '<button href="' . $escapedUrl . '" type="button" class="btn btn-danger delete_record">Delete</button>';
}

function btn_custom($url, $title, $ajax = false)
{
    $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    if ($ajax) {
        return '<button type="button" class="btn btn-success add-variant-btn" data-url="' . $escapedUrl . '">' . $escapedTitle . '</button>';
    } else {
        return '<a href="' . $escapedUrl . '" class="btn btn-success add-variant-btn">' . $escapedTitle . '</a>';
    }
}

function image_show($url, $height = 200, $width = "auto")
{
    $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $heightAttr = is_numeric($height) ? $height . 'px' : htmlspecialchars($height, ENT_QUOTES, 'UTF-8');
    $widthAttr = is_numeric($width) ? $width . 'px' : htmlspecialchars($width, ENT_QUOTES, 'UTF-8');

    return '
        <a href="' . $escapedUrl . '" class="show_image" target="_blank">
            <img src="' . $escapedUrl . '" style="height:' . $heightAttr . '; width:' . $widthAttr . '; object-fit:cover; border-radius:6px;" alt="Preview">
        </a>
    ';
}

function image_show_with_delete($url, $height = 200, $width = 200, $deletedUrl = null)
{
    $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $escapedDeletedUrl = $deletedUrl ? htmlspecialchars($deletedUrl, ENT_QUOTES, 'UTF-8') : '';
    $heightAttr = is_numeric($height) ? $height . 'px' : htmlspecialchars($height, ENT_QUOTES, 'UTF-8');
    $widthAttr = is_numeric($width) ? $width . 'px' : htmlspecialchars($width, ENT_QUOTES, 'UTF-8');

    return '
    <div class="image-wrapper" 
         style="--img-h:' . $heightAttr . '; --img-w:' . $widthAttr . ';">
        
        <span 
            class="delete-image-btn image_delete_btn" 
            data-url="' . $escapedDeletedUrl . '">
            ✕
        </span>

        <a href="' . $escapedUrl . '" class="show_image" target="_blank">
            <img src="' . $escapedUrl . '" class="preview-image" alt="Preview">
        </a>
    </div>';
}

function view_payment_status(TransactionStatus|string $status): string
{
    try {
        // Convert string to enum value if needed
        $value = $status instanceof TransactionStatus ? $status->value : $status;

        return match ($value) {
            TransactionStatus::PENDING->value =>
            '<span class="badge bg-label-warning">Pending</span>',

            TransactionStatus::PROCESSING->value =>
            '<span class="badge bg-label-info">Processing</span>',

            TransactionStatus::COMPLETED->value =>
            '<span class="badge bg-label-success">Completed</span>',

            TransactionStatus::FAILED->value =>
            '<span class="badge bg-label-danger">Failed</span>',

            TransactionStatus::CANCELLED->value =>
            '<span class="badge bg-label-danger">Cancelled</span>',

            TransactionStatus::REFUNDED->value =>
            '<span class="badge bg-label-success">Refunded</span>',

            default =>
            '<span class="badge bg-label-secondary">Unknown</span>',
        };
    } catch (\Throwable $e) {
        return '<span class="badge bg-label-secondary">Error</span>';
    }
}

function view_order_status(OrderStatus|string $status): string
{
    try {
        // Convert string to enum value if needed
        $value = $status instanceof OrderStatus ? $status->value : $status;

        return match ($value) {
            OrderStatus::PENDING_PAYMENT->value =>
            '<span class="badge bg-label-warning">Pending Payment</span>',



            OrderStatus::PAYMENT_RECEIVED->value,
            OrderStatus::CONFIRMED->value =>
            '<span class="badge bg-label-success">Confirmed</span>',

            OrderStatus::PROCESSING->value =>
            '<span class="badge bg-label-info">Processing</span>',

            OrderStatus::PACKED->value =>
            '<span class="badge bg-label-primary">Packed</span>',

            OrderStatus::SHIPPED->value =>
            '<span class="badge bg-label-info">In Transit</span>',

            OrderStatus::OUT_FOR_DELIVERY->value =>
            '<span class="badge bg-label-warning">Out for Delivery</span>',

            OrderStatus::DELIVERED->value =>
            '<span class="badge bg-label-success">Delivered</span>',

            OrderStatus::CANCEL_REQUESTED->value =>
            '<span class="badge bg-label-warning">Cancel Requested</span>',

            OrderStatus::CANCELLED->value =>
            '<span class="badge bg-label-danger">Cancelled</span>',

            // OrderStatus::RETURN_REQUESTED->value =>
            // '<span class="badge bg-label-warning">Return Requested</span>',

            // OrderStatus::RETURN_APPROVED->value =>
            // '<span class="badge bg-label-info">Return Approved</span>',

            // OrderStatus::RETURNED->value =>
            // '<span class="badge bg-label-primary">Returned</span>',

            // OrderStatus::REFUNDED->value =>
            // '<span class="badge bg-label-success">Refunded</span>',

            OrderStatus::FAILED->value =>
            '<span class="badge bg-label-danger">Payment Failed</span>',

            default =>
            '<span class="badge bg-label-secondary">Unknown</span>',
        };
    } catch (\Throwable $e) {
        return '<span class="badge bg-label-secondary">Error</span>';
    }
}

function view_rating(int $rating): string
{
    $rating = max(0, min(5, $rating)); // clamp 0–5

    return sprintf(
        '<div class="rating d-flex align-items-center">%s%s<span class="ms-1 fw-semibold">%d.0</span></div>',
        str_repeat('<i class="ri-star-fill text-warning"></i>', $rating),
        str_repeat('<i class="ri-star-line text-warning"></i>', 5 - $rating),
        $rating
    );
}

function status_dropdown($selected = null, $data = [])
{
    $id = htmlspecialchars($data['id'] ?? '', ENT_QUOTES, 'UTF-8');
    $url = htmlspecialchars($data['url'] ?? '', ENT_QUOTES, 'UTF-8');
    $method = htmlspecialchars($data['method'] ?? 'PUT', ENT_QUOTES, 'UTF-8');

    return '
        <select class="form-select change-status"
            data-id="' . $id . '"
            data-url="' . $url . '"
            data-method="' . $method . '">
            
            <option value="1" ' . ($selected == 1 ? 'selected' : '') . '>Active</option>
            <option value="0" ' . ($selected == 0 ? 'selected' : '') . '>Inactive</option>
        </select>
    ';
}

function status_custom_dropdown($selected = null, $data = [])
{
    $id = htmlspecialchars($data['id'] ?? '', ENT_QUOTES, 'UTF-8');
    $url = htmlspecialchars($data['url'] ?? '', ENT_QUOTES, 'UTF-8');
    $method = htmlspecialchars($data['method'] ?? 'PUT', ENT_QUOTES, 'UTF-8');
    $level_one = htmlspecialchars($data['level_one'] ?? 'Active', ENT_QUOTES, 'UTF-8');
    $level_two = htmlspecialchars($data['level_two'] ?? 'Inactive', ENT_QUOTES, 'UTF-8');

    return '
        <select class="form-select change-status"
            data-id="' . $id . '"
            data-url="' . $url . '"
            data-method="' . $method . '">

            <option value="1" ' . ($selected == 1 ? 'selected' : '') . '>' . $level_one . '</option>
            <option value="0" ' . ($selected == 0 ? 'selected' : '') . '>' . $level_two . '</option>
        </select>
    ';
}
