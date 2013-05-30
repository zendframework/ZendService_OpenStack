<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/delete-container-metadata.html 
$result = array(
    'url' => '/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'POST',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
if (!empty($params[1])) {
    foreach ($params[1] as $key) {
        $result['header']['X-Remove-Container-Meta-' . urlencode($key)] = 'x';
    }
}
return $result;
