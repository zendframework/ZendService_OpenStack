<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendServiceTest\OpenStack\Compute;

use ZendService\OpenStack\Compute;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @subpackage UnitTests
 */
class ComputeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to compute object
     *
     * @var Compute
     */
    protected $compute;

    /**
     * Server id of testing
     *
     * @var integer
     */
    protected static $serverId;

    /**
     * Admin password of the server
     *
     * @var string
     */
    protected static $adminPass;

    public function setUp()
    {
        $this->options = array (
            'url'      => TESTS_ZENDSERVICE_OPENSTACK_URL,
            'user'     => TESTS_ZENDSERVICE_OPENSTACK_USER,
            'password' => TESTS_ZENDSERVICE_OPENSTACK_PASSWORD,
            'key'      => TESTS_ZENDSERVICE_OPENSTACK_APIKEY
        );

        $http = new HttpClient();
        if (!TESTS_ZENDSERVICE_OPENSTACK_ONLINE && $this->responseExists($this->getName())) {
            $httpAdapter = new HttpTest;
            $httpAdapter->setResponse($this->loadResponse('../../Identity/_files/testAuthenticate'));
            $http->setAdapter($httpAdapter);
        }
        $this->compute = new Compute($this->options, $http);
        if (!TESTS_ZENDSERVICE_OPENSTACK_ONLINE && $this->responseExists($this->getName())) {
            $this->compute->getHttpClient()->getAdapter()->setResponse($this->loadResponse($this->getName()));
        }
    }

    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     */
    protected function loadResponse($name)
    {
        return @file_get_contents(__DIR__ . '/_files/' . $name . '.response');
    }

    /**
     * Check if the file response exists
     *
     * @param  string $name
     * @return boolean
     */
    protected function responseExists($name)
    {
        return file_exists(__DIR__ . '/_files/' . $name . '.response');
    }

    public function tearDown()
    {
        $fileResponse = __DIR__ . '/_files/' . $this->getname() . '.response';

        if (!file_exists($fileResponse) && $this->compute->isSuccess()) {
            $httpClient = $this->compute->getApiAdapter()->getHttpClient();
            $response   = $httpClient->getResponse()->renderStatusLine() . "\r\n" .
                          $httpClient->getResponse()->getHeaders()->toString() . "\r\n" .
                          $httpClient->getResponse()->getBody();
            file_put_contents($fileResponse, $response);
        }
    }

    public function testListFlavors()
    {
        $flavors = $this->compute->listFlavors();
        $this->assertTrue(!empty($flavors));
    }

    public function testListImages()
    {
        $images = $this->compute->listImages();
        $this->assertTrue(!empty($images));
    }

    public function testCreateServer()
    {
        $options = array(
            'name'      => 'testZF2',
            'imageRef'  => '3afe97b2-26dc-49c5-a2cc-a2fc8d80c001',
            'flavorRef' => '2'
        );
        $server = $this->compute->createServer($options);
        $this->assertTrue($server instanceof Compute\Server);
        $this->assertEquals($options['name'], $server->getName());
        self::$serverId = $server->getId();
    }

    public function testGetServer()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $server = $this->compute->getServer(self::$serverId);
        $this->assertTrue($server instanceof Compute\Server);
        $this->assertEquals($server->getId(), self::$serverId);
    }

    public function testListServer()
    {
        $serverList = $this->compute->listServers();
        $this->assertTrue($serverList instanceof Compute\ServerList);
        if (!empty(self::$serverId)) {
            $found = false;
            foreach ($serverList as $server) {
                if ($server->getId() === self::$serverId) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found);
        }
    }

    public function testUpdateServer()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $options = array ( 'name' => 'newZF2test' );
        $server = $this->compute->updateServer(self::$serverId, $options);
        $this->assertTrue($server instanceof Compute\Server);
        $this->assertEquals($server->getName(), $options['name']);
    }

    public function testListAddresses()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $addresses = $this->compute->listAddresses(self::$serverId);
        $this->assertTrue(is_array($addresses));
        $this->assertTrue(isset($addresses['addresses']));
        $this->assertTrue(isset($addresses['addresses']['public']));
        $this->assertTrue(isset($addresses['addresses']['private']));
    }

    public function testListAddressesByNetwork()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $addresses = $this->compute->listAddressesByNetwork(self::$serverId, 'public');
        $this->assertTrue(is_array($addresses));
        $this->assertTrue(isset($addresses['public']));
    }

    public function testChangeAdminPass()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $this->assertTrue($this->compute->changeAdminPass(self::$serverId, '1234567890'));
    }

    public function testRebootServer()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $this->assertTrue($this->compute->rebootServer(self::$serverId));
    }

    public function testRebuildServer()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $options = array(
            'name'      => 'rebuildZF2test',
            'imageRef'  => '3afe97b2-26dc-49c5-a2cc-a2fc8d80c001',
            'adminPass' => '1234567890'
        );
        $server = $this->compute->rebuildServer(self::$serverId, $options);
        $this->assertTrue($server instanceof Compute\Server);
        $this->assertEquals($options['name'], $server->getName());
    }

    public function testResizeServer()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $this->assertTrue($this->compute->resizeServer(self::$serverId, '2'));
    }

    public function testConfirmResizeServer()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $this->assertTrue($this->compute->confirmResizeServer(self::$serverId));
    }

    public function testRevertResizeServer()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $this->assertTrue($this->compute->revertResizeServer(self::$serverId));
    }

    public function testCreateImage()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $options = array ('name' => 'imageZF2');
        $this->assertTrue($this->compute->createImage(self::$serverId, $options));
    }

    public function testGetFlavor()
    {
        $flavor = $this->compute->getFlavor('2');
        $this->assertTrue(is_array($flavor));
        $this->assertTrue(isset($flavor['flavor']));
        $this->assertEquals($flavor['flavor']['id'], '2');
    }

    public function testGetImage()
    {
        $image = $this->compute->getImage('3afe97b2-26dc-49c5-a2cc-a2fc8d80c001');
        $this->assertTrue($image instanceof Compute\Image);
        $this->assertEquals('3afe97b2-26dc-49c5-a2cc-a2fc8d80c001', $image->getId());
    }

    public function testDeleteServer()
    {
        if (empty(self::$serverId)) {
            $this->markTestSkipped('The server ID is empty');
        }
        $this->assertTrue($this->compute->deleteServer(self::$serverId));
    }
}
