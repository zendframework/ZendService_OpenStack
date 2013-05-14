<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Create_Volume.html
return array(
    'url' => '/volumes',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'volume' => $params[0]
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('200')
    )
);
