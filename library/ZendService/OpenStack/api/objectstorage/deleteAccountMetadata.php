<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/delete-account-metadata.html
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
    foreach ($params[0] as $key) {
        $result['header']['X-Remove-Account-Meta-' . urlencode($key)] = 'x';
    }
}
return $result;
