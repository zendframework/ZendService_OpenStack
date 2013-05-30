<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/copy-object.html 
return array(
    'url' => '/' . urlencode($params[2]) . '/' . urlencode($params[3]),
    'header' => array(
        'Content-Type'   => 'application/json',
        'X-Copy-From'    => '/' . urlencode($params[0]) . '/' . urlencode($params[1]),
        'Content-Length' => 0
    ),
    'method' => 'PUT',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('201')
    )
);
