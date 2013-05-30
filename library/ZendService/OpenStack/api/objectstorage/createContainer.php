<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://docs.openstack.org/api/openstack-object-storage/1.0/content/create-container.html
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
