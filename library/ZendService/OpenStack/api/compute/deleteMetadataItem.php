<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Delete_Metadata_Item-d1e5790.html
return array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]) . '/metadata/' . urlencode($params[2]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'DELETE',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
