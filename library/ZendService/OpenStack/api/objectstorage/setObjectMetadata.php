<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/update-object-metadata.html 
$result = array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'POST',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('202')
    )
);
if (!empty($params[2])) {
    foreach ($params[2] as $key => $value) {
        $result['header']['X-Object-Meta-' . urlencode($key)] = urlencode($value);
    }
}
return $result;
