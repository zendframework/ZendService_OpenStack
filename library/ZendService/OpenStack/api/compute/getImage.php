<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Get_Image_Details-d1e4848.html
return array(
    'url' => '/images/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200', '203')
    )
);
