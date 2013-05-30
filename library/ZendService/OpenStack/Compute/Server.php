<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\OpenStack\Compute;

use ZendService\OpenStack\Compute;

class Server
{
    const ERROR_PARAM_CONSTRUCT = 'You must pass a ZendService\OpenStack\Compute object and an array';
    const ERROR_PARAM_NO_NAME   = 'You must pass the server\'s name in the array (name)';
    const ERROR_PARAM_NO_ID     = 'You must pass the server\'s id in the array (id)';

    /**
     * Server's name
     *
     * @var string
     */
    protected $name;

    /**
     * Server's id
     *
     * @var string
     */
    protected $id;

    /**
     * Image id of the server
     *
     * @var string
     */
    protected $imageId;

    /**
     * Flavor id of the server
     *
     * @var string
     */
    protected $flavorId;

    /**
     * Host id
     *
     * @var string
     */
    protected $hostId;

    /**
     * Server's status
     *
     * @var string
     */
    protected $status;

    /**
     * Progress of the status
     *
     * @var integer
     */
    protected $progress;

    /**
     * Admin password, generated on a new server
     *
     * @var string
     */
    protected $adminPass;

    /**
     * Public and private IP addresses
     *
     * @var array
     */
    protected $addresses = array();

    /**
     * @var array
     */
    protected $metadata = array();

    /**
     * The service
     *
     * @var Compute
     */
    protected $service;

    /**
     * Constructor
     *
     * @param  Compute $service
     * @param  array $options
     */
    public function __construct(Compute $service, array $options)
    {
        if (!array_key_exists('name', $options)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_NAME);
        }
        if (!array_key_exists('id', $options)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_NO_ID);
        }
        $this->service = $service;
        $this->name    = $options['name'];
        $this->id      = $options['id'];
        if (isset($options['imageId'])) {
            $this->imageId= $options['imageId'];
        }
        if (isset($options['flavorId'])) {
            $this->flavorId= $options['flavorId'];
        }
        if (isset($options['hostId'])) {
            $this->hostId= $options['hostId'];
        }
        if (isset($options['status'])) {
            $this->status= $options['status'];
        }
        if (isset($options['progress'])) {
            $this->progress= $options['progress'];
        }
        if (isset($options['adminPass'])) {
            $this->adminPass= $options['adminPass'];
        }
        if (isset($options['addresses']) && is_array($options['addresses'])) {
            $this->addresses= $options['addresses'];
        }
        if (isset($options['metadata']) && is_array($options['metadata'])) {
            $this->metadata= $options['metadata'];
        }
    }

    /**
     * Get the name of the server
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the server's id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the server's image Id
     *
     * @return string
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * Get the server's flavor Id
     *
     * @return string
     */
    public function getFlavorId()
    {
        return $this->flavorId;
    }

    /**
     * Get the server's host Id
     *
     * @return string
     */
    public function getHostId()
    {
        return $this->hostId;
    }

    /**
     * Ge the server's admin password
     *
     * @return string
     */
    public function getAdminPass()
    {
        return $this->adminPass;
    }

    /**
     * Get the server's status
     *
     * @return string|bool
     */
    public function getStatus()
    {
        $data= $this->service->getServer($this->id);
        if ($data === false) {
            return false;
        }

        $data= $data->toArray();
        $this->status= $data['status'];
        return $this->status;
    }

    /**
     * Get the progress's status
     *
     * @return integer|bool
     */
    public function getProgress()
    {
        $data= $this->service->getServer($this->id);
        if ($data === false) {
            return false;
        }

        $data= $data->toArray();
        $this->progress= $data['progress'];
        return $this->progress;
    }

    /**
     * Get the private IPs
     *
     * @return array|bool
     */
    public function getPrivateIp()
    {
        if (isset($this->addresses['private'])) {
            return $this->addresses['private'];
        }
        return false;
    }

    /**
     * Get the public IPs
     *
     * @return array|bool
     */
    public function getPublicIp()
    {
        if (isset($this->addresses['public'])) {
            return $this->addresses['public'];
        }
        return false;
    }

    /**
     * Get the metadata of the container
     *
     * If $key is empty return the array of metadata
     *
     * @param  string $key
     * @return array|string
     */
    public function getMetadata($key=null)
    {
        if (!empty($key) && isset($this->metadata[$key])) {
            return $this->metadata[$key];
        }
        return $this->metadata;
    }

    /**
     * Change the name of the server
     *
     * @param  string $name
     * @return bool
     */
    public function changeName($name)
    {
        if (empty($name)) {
            return false;
        }

        $result = $this->service->updateServer($this->id, array('name' => $name));
        if (false === $result) {
            return false;
        }

        $this->name = $name;
        return true;
    }

    /**
     * Change the admin password of the server
     *
     * @param  string $password
     * @return bool
     */
    public function changeAdminPass($password)
    {
        $result =  $this->service->changeAdminPass($this->id, $password);
        if ($result) {
            $this->adminPass = $password;
        }
        return $result;
    }

    /**
     * Reboot the server
     *
     * @return bool
     */
    public function reboot($hard=false)
    {
        return $this->service->rebootServer($this->id, $hard);
    }

    /**
     * To Array
     *
     * @return array
     */
    public function toArray()
    {
        return array (
            'name'      => $this->name,
            'id'        => $this->id,
            'imageId'   => $this->imageId,
            'flavorId'  => $this->flavorId,
            'hostId'    => $this->hostId,
            'status'    => $this->status,
            'progress'  => $this->progress,
            'adminPass' => $this->adminPass,
            'addresses' => $this->addresses,
            'metadata'  => $this->metadata,
        );
    }
}
