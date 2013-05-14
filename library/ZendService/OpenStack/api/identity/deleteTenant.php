<?php
// @see http://docs.openstack.org/api/openstack-identity-service/2.0/content/DELETE_deleteUser_v2.0_users__userId__Admin_API_Service_Developer_Operations-d1e1356.html
return array(
    'url' => '/v2.0/tenants/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'DELETE',
    'response' => array(
        'valid_codes' => array('204')
    )
);
