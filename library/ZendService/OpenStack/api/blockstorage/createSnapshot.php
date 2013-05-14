<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Create_Snapshot.html
return array(
    'url' => '/snapshots',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'snapshot' => $params[0]
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
