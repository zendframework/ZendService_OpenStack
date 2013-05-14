<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/s_listcontainers.html 
return array(
    'url' => '',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200','204')
    )
);
