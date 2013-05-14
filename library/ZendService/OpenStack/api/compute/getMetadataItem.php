<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Get_Metadata_Item-d1e5507.html
return array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]) . '/metadata/key',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('200', '203')
    )
);
