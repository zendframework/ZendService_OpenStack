<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/retrieve-container-metadata.html
return array(
    'url' => '/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'HEAD',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
