<?php
// @see http://docs.openstack.org/api/openstack-identity-service/2.0/content/GET_listUserRoles_v2.0_users__userId__roles_Admin_API_Service_Developer_Operations-d1e1356.html
return array(
    'url' => '/v2.0/users/' . urlencode($params[0]) . '/roles',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'GET',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200', '203')
    )
);
