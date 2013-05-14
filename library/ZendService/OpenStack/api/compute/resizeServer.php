<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Resize_Server-d1e3707.html 
return array(
    'url' => '/servers/'. urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'resize' => array(
            'flavorRef' => $params[1]
        )  
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
