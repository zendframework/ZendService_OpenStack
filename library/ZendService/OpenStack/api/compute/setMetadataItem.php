<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Create_or_Update_a_Metadata_Item-d1e5633.html
return array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]) . '/metadata/' . urlencode($params[2]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        $params[2] => $params[3]
    )),
    'method' => 'PUT',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('200')
    )
);
