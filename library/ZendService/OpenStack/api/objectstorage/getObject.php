<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/retrieve-object.html 
return array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'valid_codes' => array('200')
    )
);
