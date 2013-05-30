<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://docs.openstack.org/api/openstack-object-storage/1.0/content/create-update-object.html
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

$result = array(
    'url' => '/' . urlencode($params[0]) . '/' . urlencode($params[1]),
    'header' => array(
        'Content-Length' => strlen($params[2]),
        'ETag' => md5($params[2])
    ),
    'method' => 'PUT',
    'body' => $params[2],
    'response' => array(
        'valid_codes' => array('201')
    )
);
if (!empty($params[3])) {
    foreach ($params[3] as $key => $value) {
        $result['header']['X-Object-Meta-' . urlencode($key)] = urlencode($value);
    }
}
if (!empty($params[4])) {
    $result['header']['X-Delete-At'] = $params[4];
}
return $result;
