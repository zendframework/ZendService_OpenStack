<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// @see http://docs.openstack.org/api/openstack-compute/2/content/Revert_Resized_Server-d1e4024.html
return array(
    'url' => '/servers/'. urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'revertResize' => null
    )),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
