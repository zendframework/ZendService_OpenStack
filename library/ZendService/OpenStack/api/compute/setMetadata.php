<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Create_or_Replace_Metadata-d1e5358.html
return array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]) . '/metadata',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'metadata' => $params[2]
    )),
    'method' => 'PUT',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('200')
    )
);
