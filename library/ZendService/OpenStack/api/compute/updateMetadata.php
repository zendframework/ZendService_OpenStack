<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Update_Metadata-d1e5208.html
return array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]) . '/metadata',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'metadata' => $params[2]
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('200')
    )
);
