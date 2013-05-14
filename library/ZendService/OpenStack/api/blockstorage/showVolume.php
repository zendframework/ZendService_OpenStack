<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Show_Volume.html 
return array(
    'url' => '/volumes/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200')
    )
);
