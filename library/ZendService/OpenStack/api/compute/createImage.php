<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// @see http://docs.openstack.org/api/openstack-compute/2/content/Create_Image-d1e4655.html
$result =  array(
    'url' => '/servers/' . urlencode($params[0]) . '/action',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'body' => array(
        'createImage' => array(
            'name'      => $params[1]['name']
        )
    ),
    'method' => 'POST',
    'response' => array(
        'format'      => 'json',
        'valid_codes' => array('202')
    )
);
if (isset($params[1]['metadata'])) {
    $result['body']['createImage']['metadata'] = $params[1]['metadata'];
}
$result['body'] = json_encode($result['body']);
return $result;
