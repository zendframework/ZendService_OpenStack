<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Reboot_Server-d1e3371.html
return array(
    'url' => '/servers/' . urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'reboot' => array(
            'type' => $params[1]
        )
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
