<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/retrieve-object-metadata.html 
return array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'HEAD',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200')
    )
);
