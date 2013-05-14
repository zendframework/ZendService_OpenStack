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


/**
 * Object Storage OpenStack API
 *
 * @see http://docs.openstack.org/api/openstack-object-storage/1.0/content/
 */
class ObjectStorage extends AbstractOpenStack
{
    const VERSION           = '1.0';
    const HEADER_AUTHTOKEN  = 'X-Auth-Token';
    const HEADER_STORAGEURL = 'X-Storage-Url';

    const ERROR_EMPTY_CONTAINER_NAME = 'The name of the container cannot be empty';

    /**
     * @param array $options
     * @param null|HttpClient $httpClient
     */
    public function __construct(array $options, HttpClient $httpClient = null)
    {
        $this->setOptions($options);
        $this->api = new Api($httpClient);
        $this->api->setApiPath(__DIR__ . '/api/objectstorage');
        $this->api->setQueryParams(array('format' => 'json'));
        $this->api->setUrl($this->options['url']);
        if (!$this->auth($this->options['user'], null, $this->options['key'])) {
            throw new Exception\RuntimeException(
                'Invalid user or API key, please check your credentials'
            );
        }
        $this->api->resetLastResponse();
    }

    /**
     * @param  string $username
     * @param  string $password
     * @param  string $key
     * @return bool
     */
    protected function auth($username, $password, $key = null)
    {
        $result = $this->api->auth($username, $key);
        if (!$this->api->isSuccess()) {
            return false;
        }

        $headers = $this->api->getHttpClient()->getResponse()->getHeaders()->toArray();
        $this->token = $headers[self::HEADER_AUTHTOKEN];
        $this->api->setHeaders(array(self::HEADER_AUTHTOKEN => $this->token));
        $this->api->setUrl($headers[self::HEADER_STORAGEURL]);
        return true;
    }

    /**
     * @param  array $options
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
        if (!isset($options['url']) || !isset($options['user']) || !isset($options['key'])) {
            throw new Exception\InvalidArgumentException(
                'You need to pass a valid array with the following keys: url, user, and key'
            );
        }
        $this->options = $options;
    }

    /**
     * @param  array $options
     * @return array
     */
    public function listContainers(array $options = array())
    {
        if (!empty($options)) {
            $this->api->setQueryParams($options);
        }
        $result = $this->api->listContainers();
        if (!empty($options)) {
            $this->api->setQueryParams();
        }
        return $result;
    }

    /**
     * @param  array $metadata
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    protected function checkMetadata($metadata = array())
    {
         if (!empty($metadata)) {
            if (count($metadata) > 90) {
                throw new Exception\InvalidArgumentException(
                    'You cannot create more than 90 metadata keys'
                );
            }
            $size = 0;
            foreach ($metadata as $key => $value) {
                if (strlen($key) > 128 || strlen($value) > 256) {
                    throw new Exception\InvalidArgumentException(
                        'Each metadata key must be less than 128 bytes and the value less than 256 bytes'
                    );
                }
                $size += strlen($key) + strlen($value);
            }
            if ($size > 4096) {
                throw new Exception\InvalidArgumentException(
                    'You have exceeded the maximum size of metadata that is 4096 bytes'
                );
            }
        }
        return true;
    }

    public function getAccountMetadata()
    {
    }

    public function setAccountMetadata()
    {
    }

    public function deleteAccountMetadata()
    {
    }

    /**
     * @param  string $name
     * @param  array $metadata
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function createContainer($name, $metadata = array())
    {
        if (empty($name)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        $this->checkMetadata($metadata);
        $this->api->createContainer($name, $metadata);
        return $this->api->isSuccess();
    }

    /**
     * @param  string $name
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteContainer($name)
    {
        if (empty($name)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        $this->api->deleteContainer($name);
        return $this->api->isSuccess();
    }

    public function listObjects()
    {
    }

    public function getContainerMetadata()
    {
    }

    public function setContainerMetadata()
    {
    }

    public function deleteContainerMetadata()
    {
    }

    public function getObject()
    {
    }

    public function setObject()
    {
    }

    public function deleteObject()
    {
    }

    public function copyObject()
    {
    }

    public function setObjectMetadata()
    {
    }

    public function getObjectMetadata()
    {
    }
}
