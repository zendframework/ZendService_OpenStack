<?php
// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Restore_Backup.html
return array(
    'url' => '/backups/' . urlencode($params[0]) . '/restore',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'restore' => array(
            'volume_id' => $params[1]
        )
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
