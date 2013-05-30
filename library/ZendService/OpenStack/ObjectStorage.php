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

    const VERSION                    = '1.0';
    const HEADER_AUTHTOKEN           = 'X-Auth-Token';
    const HEADER_STORAGEURL          = 'X-Storage-Url';
    const ERROR_EMPTY_CONTAINER_NAME = 'The name of the container cannot be empty';
    const ERROR_EMPTY_OBJECT_NAME    = 'The name of the object cannot be empty';

    /**
     * Constructor
     *
     * @param  array $options
     * @param  HttpClient $httpClient
     * @throws Exception\RuntimeException
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
     * Authentication
     *
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
     * Set options for authentication
     *
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
     * List containers
     *
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
     * Check the metadata size limit
     *
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

    /**
     * Extract metadata information from headers
     *
     * @param  array $headers
     * @param  string $XType (X-Account, X-Container, etc)
     * @return array
     */
    protected function extractMetadata(array $headers, $XType)
    {
        $result = array();
        $size   = strlen($XType);
        foreach ($headers as $key => $value) {
            if (substr($key, 0, $size + 5) === $XType . '-Meta') {
                $result['metadata'][substr($key, $size + 6)] = $value;
            } elseif (substr($key, 0, $size) === $XType) {
                $result['account'][substr($key, $size + 1)] = $value;
            }
        }
        return $result;
    }

    /**
     * Get account metadata
     *
     * @return array
     */
    public function getAccountMetadata()
    {
        $this->api->getAccountMetadata();
        $headers = $this->api->getResponseHeaders();
        return $this->extractMetadata($headers, 'X-Account');
    }

    /**
     * Set the account metadata
     * It creates or updates the account metadata.
     *
     * @param  array $metadata
     * @return bool
     */
    public function setAccountMetadata(array $metadata)
    {
        $this->api->setAccountMetadata($metadata);
        return $this->api->isSuccess();
    }

    /**
     * Delete account metadata
     *
     * @param  array $metadata to be removed
     * @return bool
     */
    public function deleteAccountMetadata(array $metadata)
    {
        $this->api->deleteAccountMetadata($metadata);
        return $this->api->isSuccess();
    }

    /**
     * Create a container
     *
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
     * Delete a container
     *
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

    /**
     * List of objects of a container
     *
     * @param  string $container
     * @param  array $options
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function listObjects($container, $options= array())
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        if (!empty($options)) {
            $this->api->setQueryParams($options);
        }
        $result = $this->api->listObjects($container);
        if (!empty($options)) {
            $this->api->setQueryParams();
        }
        return $result;
    }

    /**
     * Get Container Metadata
     *
     * @param  string $container
     * @reurn  array
     * @throws Exception\InvalidArgumentException
     */
    public function getContainerMetadata($container)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        $this->api->getContainerMetadata($container);
        $headers = $this->api->getResponseHeaders();
        return $this->extractMetadata($headers, 'X-Container');
    }

    /**
     * Set container metadata
     *
     * @param  string $container
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function setContainerMetadata($container, array $metadata)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        $this->api->setContainerMetadata($container, $metadata);
        return $this->api->isSuccess();
    }

    /**
     * Delete container metadata
     *
     * @param  string $container
     * @param  array $metadata
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteContainerMetadata($container, array $metadata)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        $this->api->deleteContainerMetadata($container, $metadata);
        return $this->api->isSuccess();
    }

    /**
     * Get object
     *
     * @param  string $container
     * @param  string $object
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function getObject($container, $object)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_OBJECT_NAME
            );
        }
        return $this->api->getObject($container, $object);
    }

    /**
     * Set object
     *
     * Store the content in a object.
     * You can specify if the object has an expire date (optional parameter)
     *
     * @param  string $container
     * @param  string $object
     * @param  string $content
     * @param  array $metadata
     * @param  string $expire
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function setObject($container, $object, $content, $metadata = array(), $expire = null)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_OBJECT_NAME
            );
        }
        $this->api->setObject($container, $object, $content, $metadata, $expire);
        return $this->api->isSuccess();
    }

    /**
     * Delete an object
     *
     * @param  string $container
     * @param  string $object
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteObject($container, $object)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_OBJECT_NAME
            );
        }
        $this->api->deleteObject($container, $object);
        return $this->api->isSuccess();
    }

    /**
     * Copy object
     *
     * @param  string $containerFrom
     * @param  string $objectFrom
     * @param  string $containerTo
     * @param  string $objectTo
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function copyObject($containerFrom, $objectFrom, $containerTo, $objectTo)
    {
        if (empty($containerFrom)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME . ' for the source'
            );
        }
        if (empty($objectFrom)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_OBJECT_NAME . ' for the source'
            );
        }
        if (empty($containerTo)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME . ' for the destination'
            );
        }
        if (empty($objectTo)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_OBJECT_NAME . ' for the destination'
            );
        }
        if ($containerFrom === $containerTo && $objectFrom === $objectTo) {
            throw new Exception\InvalidArgumentException(
                'You cannot copy an object to itself'
            );
        }
        $this->api->copyObject($containerFrom, $objectFrom, $containerTo, $objectTo);
        return $this->api->isSuccess();
    }

    /**
     * Set object metadata
     *
     * @param  string $container
     * @param  string $object
     * @param  array $metadata
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function setObjectMetadata($container, $object, array $metadata)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_OBJECT_NAME
            );
        }
        $this->api->setObjectMetadata($container, $object, $metadata);
        return $this->api->isSuccess();
    }

    /**
     * Get object metadata
     *
     * @param  string $container
     * @param  string $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function getObjectMetadata($container, $object)
    {
        if (empty($container)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_CONTAINER_NAME
            );
        }
        if (empty($object)) {
            throw new Exception\InvalidArgumentException(
                self::ERROR_EMPTY_OBJECT_NAME
            );
        }
        $this->api->getObjectMetadata($container, $object);
        $headers = $this->api->getResponseHeaders();
        return $this->extractMetadata($headers, 'X-Object');
    }
}
