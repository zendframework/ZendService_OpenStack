<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendService\OpenStack\BlockStorage\Extensions;

use ZendService\OpenStack\AbstractOpenStack;
use Zend\Http\Client as HttpClient;

/**
 * Backup - Block Storage Extension OpenStack API
 *
 * @see http://docs.openstack.org/api/openstack-block-storage/2.0/content/Backups.html
 */
class Backup extends AbstractOpenStack
{
    const VERSION = '2.0';

    /**
     * @param array $options
     * @param null|HttpClient $httpClient
     */
    public function __construct(array $options, HttpClient $httpClient = null)
    {
        parent::__construct($options, $httpClient);

        $this->api->setApiPath(__DIR__ . '/../../api/blockstorage/extensions');

        $region = isset($options['region']) ? $options['region'] : null;
        $url    = $this->getPublicUrl('volume', null, $region);

        if (false !== $url) {
            $this->api->setUrl($url);
        }
    }

    /**
     * Create backup
     *
     * @param  array $options
     * @return array|bool
     */
    public function createBackup(array $options)
    {
        return $this->api->createBackup($options);
    }

    /**
     * List backup
     *
     * @param  bool $details
     * @return array|bool
     */
    public function listBackup($details = false)
    {
        $details = $details ? '/details' : '';
        return $this->api->listBackup($details);
    }

    /**
     * Show backup
     *
     * @param  string $id
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function showBackup($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the backup\'s ID'
            );
        }
        return $this->api->showBackup($id);
    }

    /**
     * Delete backup
     *
     * @param  string $id
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteBackup($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the backup\'s ID'
            );
        }
        $this->api->deleteBackup($id);
        return $this->api->isSuccess();
    }

    /**
     * Restore a backup
     *
     * @param  string $id
     * @param  string $volumeId
     * @return array|bool
     * @throws Exception\InvalidArgumentException
     */
    public function restoreBackup($id, $volumeId)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the backup\'s ID'
            );
        }
        if (empty($volumeId)) {
            throw new Exception\InvalidArgumentException(
                'You must specify the volume\'s ID'
            );
        }
        return $this->api->restoreBackup($id, $volumeId);
    }
}
