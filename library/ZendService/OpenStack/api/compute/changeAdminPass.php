<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Change_Password-d1e3234.html
return array(
    'url' => '/servers/' . urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'changePassword' => array(
            'adminPass'  => $params[1]
        )
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
