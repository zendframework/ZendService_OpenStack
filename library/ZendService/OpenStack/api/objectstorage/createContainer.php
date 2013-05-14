<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/create-container.html 
$result = array(
    'url' => '/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'PUT',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('201','202')
    )
);
if (!empty($params[1])) {
    foreach ($params[1] as $key => $value) {
        $result['header']['X-Container-Meta-' . urlencode($key)] = urlencode($value);
    }
}
return $result;
