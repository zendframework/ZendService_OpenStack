<?php
// @see http://docs.openstack.org/api/openstack-identity-service/2.0/content/POST_addUser_v2.0_users_Admin_API_Service_Developer_Operations-d1e1356.html
return array(
    'url' => '/v2.0/users',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'POST',
    'body' => json_encode(array(
        'user' => array(
            'username' => $params[0]['username'],
            'email'    => $params[0]['email'],
            'enabled'  => true,
            'OS-KSADM:password' => $params[0]['password']
        )
    )),
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('201')
    )
);
