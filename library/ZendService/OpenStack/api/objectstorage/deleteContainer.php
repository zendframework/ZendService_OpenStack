<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://docs.openstack.org/api/openstack-object-storage/1.0/content/delete-container.html
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'url' => '/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'DELETE',
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('204')
    )
);
