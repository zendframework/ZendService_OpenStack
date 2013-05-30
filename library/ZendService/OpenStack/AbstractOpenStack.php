<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\OpenStack;

use Traversable;
use ZendService\Api\Api;
use Zend\Http\Client as HttpClient;
use Zend\Stdlib\ArrayUtils;

abstract class AbstractOpenStack
{
    const HEADER_AUTHTOKEN = "X-Auth-Token";

    /**
     * Token of authentication
     *
     * @var string
     */
    protected $token;

    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Api
     *
     * @var Api
     */
    protected $api;

    /**
     * Error Msg
     *
     * @var string
     */
    protected $errorMsg;

    /**
     * HTTP error code
     *
     * @var string
     */
    protected $errorCode;

    /**
     * Available services
     *
     * @var array
     */
    protected $services;

    /**
     * Raw response (false means Object response for some APIs)
     *
     * @var boolean
     */
    protected $rawResponse = false;

    /**
     * Constructor
     *
     * You must pass the OpenStack's options for the authentication
     *
     * @param  array $options
     * @param  HttpClient $httpClient
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(array $options, HttpClient $httpClient = null)
    {
        $this->setOptions($options);
        $this->api = new Api($httpClient);
        $this->api->setApiPath(__DIR__ . '/api/identity');
        $this->api->setQueryParams(array('format' => 'json'));
        $this->api->setUrl($this->options['url']);
        $username = isset($options['user']) ? $options['user'] : $options['username'];
        if (isset($options['password'])) {
            if (!$this->auth($username, $options['password'])) {
                throw new Exception\RuntimeException(
                    'Authentication failed, please check the username and password provided'
                );
            }
        } else {
            if (!$this->auth($username, null, $options['key'])) {
                throw new Exception\RuntimeException(
                    'Authentication failed, please check the username and the API\'s key provided'
                );
            }
        }
        $this->api->resetLastResponse();
    }

    /**
     * Authentication API
     *
     * @param  string $username
     * @param  string $password
     * @param  string $key
     * @return bool
     */
    protected function auth($username, $password, $key = null)
    {
        if (empty($key)) {
            $result = $this->api->authenticate($username, $password);
        } else {
            $result = $this->api->authenticateByKey($username, $key);
        }
        if ($this->api->isSuccess()) {
            if (isset($result['access']['token']['id'])) {
                $this->token = $result['access']['token']['id'];
                $this->api->setHeaders(array( self::HEADER_AUTHTOKEN => $this->token ));
                $this->services = $result['access']['serviceCatalog'];
                return true;
            }
        }
        return false;
    }

    /**
     * Get the public URL for the service
     *
     * @param  string $type type of service
     * @param  string $version
     * @param  string $region
     * @return string|bool
     */
    public function getPublicUrl($type, $version = null, $region = null)
    {
        foreach ($this->services as $service) {
            if ($service['type'] === strtolower($type)) {
                foreach ($service['endpoints'] as $endpoint) {
                    if ((empty($region) || $endpoint['region'] === $region) &&
                        (empty($version) || ($endpoint['versionId'] === $version || $endpoint['versionId'] . '.0'  === $version))) {
                        return $endpoint['publicURL'];
                    }
                }
            }
        }
        return false;
    }

    /**
     * Set the API response to raw format
     *
     * @param bool $enabled
     */
    public function setRawResponse(boolean $enabled)
    {
        $this->rawResponse = $enabled;
        return $this;
    }

    /**
     * Get the API response format
     *
     * @return bool
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * Set the options for the API authentication
     *
     * @param  array $options
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    protected function setOptions(array $options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }
        if (!isset($options['url'])) {
            throw new Exception\InvalidArgumentException(
                'You must specify the URL of the API entrypoint'
            );
        }
        if (!isset($options['user']) && !isset($options['username'])) {
            throw new Exception\InvalidArgumentException(
                'You must specify the username for the API authentication'
            );
        }
        if (!isset($options['password']) && !isset($options['key'])) {
            throw new Exception\InvalidArgumentException(
                'You must specify the password or the API key for the authentication'
            );
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Get the config parameters
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get the API adapter
     *
     * @return Api
     */
    public function getApiAdapter()
    {
        return $this->api;
    }

    /**
     * Set the API adapter
     *
     * @param  Api $api
     * @return self
     */
    public function setApiAdapter(Api $api)
    {
        $this->api = $api;
        return $this;
    }

    /**
     * Get the error msg of the last HTTP call
     *
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->api->getErrorMsg();
    }

    /**
     * Get the error code of the last HTTP call
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->api->getStatusCode();
    }

    /**
     * Return true is the last call was successful
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->api->isSuccess();
    }

    /**
     * Return the authentication token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Return the available services
     *
     * @return array
     */
    public function getAvailableServices()
    {
        return $this->services;
    }

    /**
     * Get the HTTP client
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->api->getHttpClient();
    }

    /**
     * Set the HTTP client
     *
     * @param  HttpClient $http
     * @return self
     */
    public function setHttpClient(HttpClient $http)
    {
        $this->api->setHttpClient($http);
        return $this;
    }

    /**
     * Set the API URL
     *
     * @param  string $url
     * @return self
     */
    public function setUrl($url)
    {
        $this->api->setUrl($url);
        return $this;
    }

    /**
     * Get the API URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->api->getUrl();
    }
}
