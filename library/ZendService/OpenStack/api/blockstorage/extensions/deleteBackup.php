<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Delete_Backup.html 
return array(
    'url' => '/backups/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'DELETE',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('202')
    )
);
