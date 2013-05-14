<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/List_Flavors-d1e4188.html
return array(
    'url' => '/flavors' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200', '203')
    )
);
