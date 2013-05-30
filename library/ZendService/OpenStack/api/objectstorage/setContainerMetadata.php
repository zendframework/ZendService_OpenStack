<?php
// @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/Update_Container_Metadata-d1e1900.html 
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
    foreach ($params[1] as $key => $value) {
        $result['header']['X-Container-Meta-' . urlencode($key)] = urlencode($value);
    }
}
return $result;
