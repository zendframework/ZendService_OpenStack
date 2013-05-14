<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Confirm_Resized_Server-d1e3868.html 
return array(
    'url' => '/servers/'. urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'confirmResize' => null  
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('204')
    )
);
