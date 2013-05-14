<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Revert_Resized_Server-d1e4024.html 
return array(
    'url' => '/servers/'. urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'revertResize' => null  
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
