<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/delete-object.html 
return array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'DELETE',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
