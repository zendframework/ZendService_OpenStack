<?php
// @see http://docs.openstack.org/api/openstack-compute/2/content/Create_Image-d1e4655.html
$result =  array(
    'url' => '/servers/' . urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => array(
        'createImage' => array(
            'name'      => $params[1]['name']
        )
    ),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
if (isset($params[1]['metadata'])) {
    $result['body']['createImage']['metadata'] = $params[1]['metadata'];
}
$result['body'] = json_encode($result['body']);
return $result;
