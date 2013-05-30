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
 * Block Storage OpenStack API
 *
 * @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/
 */
class BlockStorage extends AbstractOpenStack
{
    const VERSION = '2.0';

    /**
     * @param array $options
     * @param null|HttpClient $httpClient
     */
    public function __construct(array $options, HttpClient $httpClient = null)
    {
        parent::__construct($options, $httpClient);
        $this->api->setApiPath(__DIR__ . '/api/blockstorage');
        $region = isset($options['region']) ? $options['region'] : null;
        $url = $this->getPublicUrl('volume', null, $region);
        if (false !== $url) {
            $this->api->setUrl($url);
        }
    }

    /**
     * Create a volume
     *
     * @param  array $options
     * @return array|bool
     */
    public function createVolume(array $options)
    {
        return $this->api->createVolume($options);
    }

    /**
     * List volume
     *
     * @param  bool $details
     * @return array
     */
    public function listVolume($details = false)
    {
        $details = $details ? '/details' : '';
        return $this->api->listVolume($details);
    }

    /**
     * Show volume
     *
     * @param  string $id
     * @return array|bool
     */
    public function showVolume($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the volume\'s ID'
            );
        }
        return $this->api->showVolume($id);
    }

    /**
     * Update volume
     *
     * @param  string $id
     * @param  array $options
     * @return array|bool
     */
    public function updateVolume($id, array $options)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the volume\'s ID'
            );
        }
        return $this->api->updateVolume($id, $options);
    }

    /**
     * Delete volume
     *
     * @param string $id
     * @return bool
     */
    public function deleteVolume($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the volume\'s ID'
            );
        }
        $this->api->deleteVolume($id);
        return $this->api->isSuccess();
    }

    /**
     * Create snapshot
     *
     * @param  array $options
     * @return array|bool
     */
    public function createSnapshot(array $options)
    {
        return $this->api->createSnapshot($options);
    }

    /**
     * List snapshot
     *
     * @param  bool $details
     * @return array|bool
     */
    public function listSnapshot($details = false)
    {
        $details = $details ? '/details' : '';
        return $this->api->listSnapshot($details);
    }

    /**
     * Show snapshot
     *
     * @param  string $id
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function showSnapshot($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the snapshot\'s ID'
            );
        }
        return $this->api->showSnapshot($id);
    }

    /**
     * Update snapshot
     *
     * @param  string $id
     * @param  array $options
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function updateSnapshot($id, array $options)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the snapshot\'s ID'
            );
        }
        return $this->api->updateSnapshot($id, $options);
    }

    /**
     * Delete snapshot
     *
     * @param  string $id
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteSnapshot($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the snapshot\'s ID'
            );
        }
        $this->api->deleteSnapshot($id);
        return $this->api->isSuccess();

    }

    /**
     * List volume type
     *
     * @return array|bool
     */
    public function listVolumeType()
    {
        return $this->api->listVolumeType();
    }

    /**
     * Show volume type
     *
     * @param string $id
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function showVolumeType($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the volume type\'s ID'
            );
        }
        return $this->api->showVolumeType($id);
    }
}
