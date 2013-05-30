<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/create-update-account-metadata.html 
$result = array(
    'url' => '',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'POST',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
if (!empty($params[0])) {
    foreach ($params[0] as $key => $value) {
        $result['header']['X-Account-Meta-' . urlencode($key)] = urlencode($value);
    }
}
return $result;
