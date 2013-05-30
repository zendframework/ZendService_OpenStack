<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Update_Snapshot.html
return array(
    'url' => '/snapshots/' . urlencode($params[0]),
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => json_encode(array(
        'snapshot' => $params[1]
    )),
    'method' => 'PUT',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('200')
    )
);
