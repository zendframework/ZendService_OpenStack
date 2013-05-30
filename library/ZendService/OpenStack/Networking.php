<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\OpenStack;

/**
 * Networking OpenStack API
 *
 * @see http://docs.openstack.org/api/openstack-network/2.0/content/
 */
class Networking extends AbstractOpenStack
{
    const VERSION = '2.0';

    /**
     * @todo  complete the constructor
     * @param array $options
     * @param null|HttpClient $httpClient
     */
    public function __construct(array $options, HttpClient $httpClient = null)
    {
        parent::__construct($options, $httpClient);
        $this->api->setApiPath(__DIR__ . '/api/networking');
    }

    public function listNetworks()
    {
    }

    public function showNetworks()
    {
    }

    public function createNetwork()
    {
    }

    public function updateNetwork()
    {
    }

    public function deleteNetwork()
    {
    }

    public function listSubnets()
    {
    }

    public function showSubnet()
    {
    }

    public function createSubnet()
    {
    }

    public function updateSubnet()
    {
    }

    public function deleteSubnet()
    {
    }

    public function listPorts()
    {
    }

    public function showPort()
    {
    }

    public function createPort()
    {
    }

    public function updatePort()
    {
    }

    public function deletePort()
    {
    }
}
