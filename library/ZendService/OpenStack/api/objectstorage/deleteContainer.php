<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/delete-container.html 
return array(
    'url' => '',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'DELETE',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
