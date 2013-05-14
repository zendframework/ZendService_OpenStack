<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/List_Snapshots.html 
return array(
    'url' => '/snapshots' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200')
    )
);
