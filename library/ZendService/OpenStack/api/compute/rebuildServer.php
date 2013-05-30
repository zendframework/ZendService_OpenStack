<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// @see http://docs.openstack.org/api/openstack-compute/2/content/Rebuild_Server-d1e3538.html
$result =  array(
    'url' => '/servers/' . urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => array(
        'rebuild' => array(
            'name'      => $params[1]['name'],
            'imageRef'  => $params[1]['imageRef'],
            'adminPass' => $params[1]['adminPass']
        )
    ),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
if (isset($params[1]['accessIPv4'])) {
    $result['body']['rebuild']['accessIPv4'] = $params[1]['accessIPv4'];
}
if (isset($params[1]['accessIPv6'])) {
    $result['body']['rebuild']['accessIPv6'] = $params[1]['accessIPv6'];
}
if (isset($params[1]['metadata'])) {
    $result['body']['rebuild']['metadata'] = $params[1]['metadata'];
}
if (isset($params[1]['file'])) {
    $content = '';
    if (isset($params[1]['content'])) {
        $content = $params[1]['content'];
    }
    $result['body']['rebuild']['personality'] = array(
        'path'     => $params[1]['file'],
        'contents' => $content
    );
}
$result['body'] = json_encode($result['body']);
return $result;
