<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/retrieve-account-metadata.html 
return array(
    'url' => '',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'HEAD',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
