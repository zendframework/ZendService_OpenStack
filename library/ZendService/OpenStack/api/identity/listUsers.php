<?php
// @see http://docs.openstack.org/api/openstack-identity-service/2.0/content/GET_listUsers_v2.0_users_Admin_API_Service_Developer_Operations-d1e1356.html 
return array(
    'url' => '/v2.0/users',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200', '203')
    )
);
