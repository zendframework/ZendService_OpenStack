<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://docs.openstack.org/api/openstack-object-storage/1.0/content/authentication-object-dev-guide.html
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'url' => '',
    'header' => array(
        'X-Auth-User' => $params[0],
        'X-Auth-Key'  => $params[1]
    ),
    'method' => 'GET',
    'response' => array(
        'valid_codes' => array('200','201','202','203','204','205','206','207')
    )
);
