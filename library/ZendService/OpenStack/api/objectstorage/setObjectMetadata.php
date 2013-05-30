<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://docs.openstack.org/api/openstack-object-storage/1.0/content/update-object-metadata.html
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
