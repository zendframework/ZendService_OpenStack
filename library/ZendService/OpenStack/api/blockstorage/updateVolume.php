<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Update_Volume.html 
return array(
    'url' => '/volumes/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'volume' => $params[1]
    )),
    'method' => 'PUT',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('200')
    )
);
