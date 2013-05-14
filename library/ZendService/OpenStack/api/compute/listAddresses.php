<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/List_Addresses-d1e3014.html
return array(
    'url' => '/servers/' . urlencode($params[0]) . '/ips',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200', '203')
    )
);
