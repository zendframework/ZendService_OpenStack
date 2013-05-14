<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Delete_Image-d1e4957.html
return array(
    'url' => '/images/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'DELETE',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
