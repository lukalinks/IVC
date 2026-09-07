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
