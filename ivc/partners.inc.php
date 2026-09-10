<?php
function ivc_business_types()
{
    return array(
        'hotel' => 'Hotel',
        'resort' => 'Resort',
        'travel_agent' => 'Travel Agency',
        'car_rental' => 'Car Rental',
        'tour' => 'Tour Service',
        'other' => 'Other',
    );
}

if (!function_exists('ivc_is_admin')) {
    include_once __DIR__ . '/admin.inc.php';
}

function ivc_listing_type_label($type)
{
    $types = ivc_business_types();
    return isset($types[$type]) ? $types[$type] : 'Other';
}

function ivc_user_latest_listing($uid)
{
    $uid = (int) $uid;
    if ($uid <= 0 || empty($GLOBALS['mysqli'])) {
        return null;
    }
    $res = @$GLOBALS['mysqli']->query("SELECT * FROM ivc_listings WHERE uid=$uid ORDER BY id DESC LIMIT 1");
    if (!$res || $res->num_rows === 0) {
        return null;
    }
    return $res->fetch_assoc();
}

function ivc_is_approved_partner($uid)
{
    $uid = (int) $uid;
    if ($uid <= 0 || empty($GLOBALS['mysqli'])) {
        return false;
    }
    $res = @$GLOBALS['mysqli']->query("SELECT id FROM ivc_listings WHERE uid=$uid AND status='approved' LIMIT 1");
    return $res && $res->num_rows > 0;
}

function ivc_partner_redirect_for_uid($uid)
{
    if (ivc_user_latest_listing($uid)) {
        return 'partner_status.php';
    }
    return 'partner_submit.php';
}

function ivc_partner_status_label($status)
{
    $status = strtolower((string) $status);
    if ($status === 'approved') {
        return 'Approved';
    }
    if ($status === 'rejected') {
        return 'Rejected';
    }
    return 'Pending review';
}
