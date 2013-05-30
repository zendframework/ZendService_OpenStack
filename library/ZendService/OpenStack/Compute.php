<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\OpenStack;

use Zend\Http\Client as HttpClient;

/**
 * Compute OpenStack API
 *
 * @see http://docs.openstack.org/api/openstack-compute/2/content/index.html
 */
class Compute extends AbstractOpenStack
{
    const API_VERSION = '2.0';

    /**
     * @param array $options
     * @param null|HttpClient $httpClient
     */
    public function __construct(array $options, HttpClient $httpClient = null)
    {
        parent::__construct($options, $httpClient);
        $this->api->setApiPath(__DIR__ . '/api/compute');

        $region = isset($options['region']) ? $options['region'] : null;
        $url    = $this->getPublicUrl('compute', self::API_VERSION, $region);

        if (false !== $url) {
            $this->api->setUrl($url);
        }
    }

    /**
     * Get the list of the servers
     * If $details is true returns detailed info
     *
     * $options = array(
     *     'image'         => 'the imageRef',
     *     'flavor'        => 'the flavorRef',
     *     'name'          => 'the name of the server',
     *     'status'        => 'the status of the server',
     *     'marker'        => 'the marker to start from',
     *     'limit'         => 'max number of servers to return',
     *     'changes-since' => 'filter on the change since',
     * );
     *
     * @param  arrray  $options
     * @param  bool $details
     * @return Compute\ServerList|array|bool
     */
    public function listServers(array $options = array(), $details = false)
    {
        if (!empty($options)) {
            $this->api->setQueryParams($options);
        }
        $result = $this->api->listServers($details ? '/detail' : '');
        if (!empty($options)) {
            $this->api->setQueryParams();
        }
        if (!$this->getRawResponse() && isset($result['servers'])) {
            return new Compute\ServerList($this, $result['servers']);
        }
        return $result;
    }

    /**
     * Create a server
     *
     * The configuration data are specified by $options = array(
     *     'name'        => 'the name of the server',
     *     'imageRef'    => 'the image to use for the server',
     *     'flavorRef'   => 'the configuration reference for the server',
     *     'metadata'    => 'the array of metadata', // (optional)
     *     'file'        => 'the path of the file to create on the server', // (optional)
     *     'content'     => 'the content of the file', // (optional)
     * )
     *
     * @param  array $options
     * @return Compute\Server|array|bool
     */
    public function createServer(array $options)
    {
        if (empty($options) || !isset($options['name']) || !isset($options['imageRef']) ||
            !isset($options['flavorRef'])) {
            throw new Exception\InvalidArgumentException(
                'The options must be an array with the following values: name, imageRef and flavorRef'
            );
        }
        $result = $this->api->createServer($options);
        // Some cloud vendors doesn't return the name of the server
        // according to the OpenStack specification they should
        // @see http://docs.openstack.org/api/openstack-compute/2/content/CreateServers.html
        if (!isset($result['server']['name'])) {
            $result['server']['name'] = $options['name'];
        }
        if (!$this->getRawResponse() && isset($result['server'])) {
            return new Compute\Server($this, $result['server']);
        }
        return $result;
    }

    /**
     * Get Server
     *
     * @param  string $id
     * @return Compute\Server|bool
     */
    public function getServer($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the server\'s ID'
            );
        }
        $result = $this->api->getServer($id);
        if (!$this->getRawResponse() && isset($result['server'])) {
            return new Compute\Server($this, $result['server']);
        }
        return $result;
    }

    /**
     * Update the server
     *
     * The values to change are specified using $options = array(
     *    'name'       => 'the new name of the server',
     *    'accessIPv4' => 'the new IPv4 address of the server',
     *    'accessIPv6' => 'the new IPv6 address of the server',
     * )
     *
     * @param  array $options
     * @return Compute\Server|bool
     */
    public function updateServer($id, array $options)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the server\'s ID'
            );
        }
        if (empty($options) || (!isset($options['name']) &&
            !isset($options['accessIPv4']) && !isset($options['accessIPv6']))) {
            throw new Exception\InvalidArgumentException(
                'The options must be an array with following keys: server\'s ID, name or accessIPv4 and accessIPv6'
            );
        }
        $result = $this->api->updateServer($id, $options);
        if (!$this->getRawResponse() && isset($result['server'])) {
            return new Compute\Server($this, $result['server']);
        }
        return $result;
    }

    /**
     * Delete a server
     *
     * @param  string $id
     * @return bool
     */
    public function deleteServer($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the server\'s ID'
            );
        }
        $this->api->deleteServer($id);
        return $this->api->isSuccess();
    }

    /**
     * List addresses of a server
     *
     * @param  string $id
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function listAddresses($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        return $this->api->listAddresses($id);
    }

    /**
     * List addressed of a server by network
     *
     * @param  string $id
     * @param  string $network
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function listAddressesByNetwork($id, $network)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($network)) {
            throw new Exception\InvalidArgumentException('
                You must specify the network'
            );
        }
        return $this->api->listAddressesByNetwork($id, $network);
    }

    /**
     * Change the admin password of the server
     *
     * @param  string $id
     * @param  string $password
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function changeAdminPass($id, $password)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($password)) {
            throw new Exception\InvalidArgumentException('
                You must specify the new admin password'
            );
        }
        $this->api->changeAdminPass($id, $password);
        return $this->api->isSuccess();
    }

    /**
     * Reboot a server
     *
     * @param  string $id
     * @param  bool $soft
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function rebootServer($id, $soft = true)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        $type = $soft ? 'SOFT' : 'HARD';
        $this->api->rebootServer($id, $type);
        return $this->api->isSuccess();
    }

    /**
     * Rebuild a server
     *
     * The options is an array with the follwing structure = array(
     *     'name'       => The new name of the server,
     *     'imageRef'   => The image reference. Specify as an ID or full URL,
     *     'adminPass'  => The administrator password,
     *     'accessIPv4' => The IP version 4 address (optional),
     *     'accessIPv6' => The IP version 6 address (optional),
     *     'metadata'   => Array of metadata (optional),
     *     'personality => array(  (optional)
     *         'file'     => The file path,
     *         'contents' => The file content,
     *     ),
     * )
     *
     * @param  string $id
     * @param  array $options
     * @return Compute\Server|bool
     * @throws Exception\InvalidArgumentException
     */
    public function rebuildServer($id, array $options)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($options) || !isset($options['name']) ||
            !isset($options['imageRef']) || !isset($options['adminPass'])) {
            throw new Exception\InvalidArgumentException(
                'The options must be an array with following keys: name, imageRef, adminPass'
            );
        }
        $result = $this->api->rebuildServer($id, $options);
        if (!$this->getRawResponse() && isset($result['server'])) {
            return new Compute\Server($this, $result['server']);
        }
        return $result;
    }

    /**
     * Resize a server
     *
     * @param  string $id
     * @param  string $flavorRef
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function resizeServer($id, $flavorRef)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($flavorRef)) {
            throw new Exception\InvalidArgumentException('
                You must specify the new flavorRef of the server'
            );
        }
        $this->api->resizeServer($id, $flavorRef);
        return $this->api->isSuccess();
    }

    /**
     * Confirm resize of the server
     *
     * @param  string $id
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function confirmResizeServer($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        $this->api->confirmResizeServer($id);
        return $this->api->isSuccess();
    }

    /**
     * Revert resize of a server
     *
     * @param  string $id
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function revertResizeServer($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        $this->api->revertResizeServer($id);
        return $this->api->isSuccess();

    }

    /**
     * Create an image based on a server
     *
     * @param  string $id
     * @param  array $options
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function createImage($id, array $options)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($options) || !isset($options['name'])) {
            throw new Exception\InvalidArgumentException(
                'The options must be an array with at least the name key'
            );
        }
        $this->api->createImage($id, $options);
        return $this->api->isSuccess();
    }

    /**
     * List of flavors
     *
     * @param  array $options
     * @param  bool $details
     * @return array|bool
     */
    public function listFlavors(array $options = array(), $details = false)
    {
        if (!empty($options)) {
            $this->api->setQueryParams($options);
        }
        $result = $this->api->listFlavors($details ? '/detail' : '');
        if (!empty($options)) {
            $this->api->setQueryParams();
        }
        return $result;
    }

    /**
     * Get the flavor
     *
     * @param  string $id
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function getFlavor($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the flavor\'s ID'
            );
        }
        return $this->api->getFlavor($id);
    }

    /**
     * List images
     *
     * @param  array $options
     * @param  bool $details
     * @return Compute\ImageList|bool
     */
    public function listImages(array $options = array(), $details = false)
    {
        if (!empty($options)) {
            $this->api->setQueryParams($options);
        }
        $result = $this->api->listImages($details ? '/detail' : '');
        if (!empty($options)) {
            $this->api->setQueryParams();
        }
        if (!$this->getRawResponse() && isset($result['images'])) {
            return new Compute\ImageList($this, $result['images']);
        }
        return $result;
    }

    /**
     * Get image
     *
     * @param  string $id
     * @return Compute\Image|bool
     * @throws Exception\InvalidArgumentException
     */
    public function getImage($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the image\'s ID'
            );
        }
        $result = $this->api->getImage($id);
        if (!$this->getRawResponse() && isset($result['image'])) {
            return new Compute\Image($this, $result['image']);
        }
        return $result;
    }

    /**
     * Delete an image
     *
     * @param  string $id
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteImage($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the image\'s ID'
            );
        }
        $this->api->deleteImage($id);
        return $this->api->isSuccess();
    }

    /**
     * List server metadata
     *
     * @param  string $id
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function listServerMetadata($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        return $this->api->listMetadata('servers', $id);
    }

    /**
     * List image metadata
     *
     * @param  string $id
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function listImageMetadata($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the image\'s ID'
            );
        }
        return $this->api->listMetadata('images', $id);
    }

    /**
     * Set the server's metadata
     *
     * @param  string $id
     * @param  array $metadata
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function setServerMetadata($id, array $metadata)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($metadata)) {
            throw new Exception\InvalidArgumentException('
                You must specify the metadata'
            );
        }
        $result = $this->api->setMetadata('servers', $id, $metadata);
        return $this->api->isSuccess();
    }

    /**
     * Set the image's metadata
     *
     * @param  string $id
     * @param  array $metadata
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function setImageMetadata($id, array $metadata)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the image\'s ID'
            );
        }
        if (empty($metadata)) {
            throw new Exception\InvalidArgumentException('
                You must specify the metadata'
            );
        }
        $result = $this->api->setMetadata('images', $id, $metadata);
        return $this->api->isSuccess();
    }

    /**
     * Update server's metadata
     *
     * @param  string $id
     * @param  array $metadata
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function updateServerMetadata($id, array $metadata)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($metadata)) {
            throw new Exception\InvalidArgumentException('
                You must specify the metadata'
            );
        }
        return $this->api->updateMetadata('servers', $id, $metadata);
    }

    /**
     * Update image's metadata
     *
     * @param  string $id
     * @param  array $metadata
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function updateImageMetadata($id, array $metadata)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the image\'s ID'
            );
        }
        if (empty($metadata)) {
            throw new Exception\InvalidArgumentException('
                You must specify the metadata'
            );
        }
        return $this->api->updateMetadata('images', $id, $metadata);
    }

    /**
     * Get server's metadata item
     *
     * @param  string $id
     * @param  string $key
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function getServerMetadataItem($id, $key)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('
                You must specify the key of the metadata'
            );
        }
        return $this->api->getMetadataItem('servers', $id, $key);
    }

    /**
     * Get image's metadata item
     *
     * @param  string $id
     * @param  string $key
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function getImageMetadataItem($id, $key)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the image\'s ID'
            );
        }
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('
                You must specify the key of the metadata'
            );
        }
        return $this->api->getMetadataItem('images', $id, $key);
    }

    /**
     * Set server's metadata item
     *
     * @param  string $id
     * @param  string $key
     * @param  string $value
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function setServerMetadataItem($id, $key, $value = '')
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('
                You must specify the key of the metadata'
            );
        }
        $this->api->setMetadataItem('servers', $id, $key, $value);
        return $this->api->isSuccess();
    }

    /**
     * Set image's metadata item
     *
     * @param  string $id
     * @param  string $key
     * @param  string $value
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function setImageMetadataItem($id, $key, $value = '')
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the image\'s ID'
            );
        }
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('
                You must specify the key of the metadata'
            );
        }
        $this->api->setMetadataItem('images', $id, $key, $value);
        return $this->api->isSuccess();
    }

    /**
     * Delete server's metadata item
     *
     * @param  string $id
     * @param  string $key
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteServerMetadataItem($id, $key)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the server\'s ID'
            );
        }
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('
                You must specify the key of the metadata'
            );
        }
        $this->api->deleteMetadataItem('servers', $id, $key);
        return $this->api->isSuccess();
    }

    /**
     * Delete image's metadata item
     *
     * @param  string $id
     * @param  string $key
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteImageMetadataItem($id, $key)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('
                You must specify the image\'s ID'
            );
        }
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('
                You must specify the key of the metadata'
            );
        }
        $this->api->deleteMetadataItem('images', $id, $key);
        return $this->api->isSuccess();
    }
}
