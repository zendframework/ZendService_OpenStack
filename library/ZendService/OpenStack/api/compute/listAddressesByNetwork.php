<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/List_Addresses_by_Network-d1e3118.html
return array(
    'url' => '/servers/' . urlencode($params[0]) . '/ips/' . urlencode($params[1]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200', '203')
    )
);
