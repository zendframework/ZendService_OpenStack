<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// @see http://docs.rackspace.com/servers/api/v2/cs-devguide/content/curl_auth.html
return array(
    'url' => '/v2.0/tokens',
    'header' => array(
        'Content-Type' => 'application/json'
    ),
    'method' => 'POST',
    'body' => json_encode(array(
        'auth' => array(
            'RAX-KSKEY:apiKeyCredentials' => array(
                'username' => $params[0],
                'apiKey'   => $params[1]
            )
        )
    )),
    'response' => array(
        'format' => 'json',
        'valid_codes' => array('200', '203')
    )
);
