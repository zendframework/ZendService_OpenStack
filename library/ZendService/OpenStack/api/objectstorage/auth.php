<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/authentication-object-dev-guide.html 
return array(
    'url' => '',
    'header' => array(
        'X-Auth-User' => $params[0],
        'X-Auth-Key'  => $params[1]
    ),
    'method' => 'GET',
    'response' => array(
        'valid_codes' => array('200','201','202','203','204','205','206','207')
    )
);
