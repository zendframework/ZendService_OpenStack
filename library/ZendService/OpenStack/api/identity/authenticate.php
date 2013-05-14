<?php
// @see http://docs.openstack.org/api/openstack-identity-service/2.0/content/POST_authenticate_v2.0_tokens_Admin_API_Service_Developer_Operations-d1e1356.html
$result = array(
    'url' => '/v2.0/tokens',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'POST',
    'body' => array(
        'auth' => array(
            'passwordCredentials' => array(
                'username' => $params[0],
                'password' => $params[1]
            )
        )
    ),
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200', '203')
    )
);
if (isset($params[2])) {
    $result['body']['auth']['tenantName'] = $params[2];
}
$result['body'] = json_encode($result['body']);
return $result;
