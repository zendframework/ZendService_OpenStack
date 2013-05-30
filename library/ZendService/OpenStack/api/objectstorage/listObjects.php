<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/list-objects.html 
return array(
    'url' => '/' . urlencode($param[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'valid_codes' => array('204')
    )
);
