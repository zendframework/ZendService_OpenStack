<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Update_Snapshot.html 
return array(
    'url' => '/snapshots/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'snapshot' => $params[1]
    )),
    'method' => 'PUT',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('200')
    )
);
