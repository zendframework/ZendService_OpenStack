<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// @see http://docs.openstack.org/api/openstack-compute/2/content/CreateServers.html
$result =  array(
    'url' => '/servers',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => array(
        'server' => array(
            'name'      => $params[0]['name'],
            'imageRef'  => $params[0]['imageRef'],
            'flavorRef' => $params[0]['flavorRef']
        )
    ),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
if (isset($params[0]['metadata'])) {
    $result['body']['server']['metadata'] = $params[0]['metadata'];
}
if (isset($params[0]['file'])) {
    $content = '';
    if (isset($params[0]['content'])) {
        $content = $params[0]['content'];
    }
    $result['body']['server']['personality'] = array(
        'path'     => $params[0]['file'],
        'contents' => $content
    );
}
$result['body'] = json_encode($result['body']);
return $result;
