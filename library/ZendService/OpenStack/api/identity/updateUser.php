<?php
// @see http://docs.openstack.org/api/openstack-identity-service/2.0/content/POST_updateUser_v2.0_users__userId__Admin_API_Service_Developer_Operations-d1e1356.html
return array(
    'url' => '/v2.0/users/' . urlencode($params[0]['id']),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'POST',
    'body' => json_encode(array(
        'user' => array(
            'id'       => $params[0]['id'],
            'username' => $params[0]['username'],
            'email'    => $params[0]['email'],
            'enabled'  => true
        )
    )),
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200')
    )
);
