<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Volume_List_Types.html 
return array(
    'url' => '/types',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200')
    )
);
