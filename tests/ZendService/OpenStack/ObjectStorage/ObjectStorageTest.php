<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendServiceTest\OpenStack\Compute;

use ZendService\OpenStack\ObjectStorage;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @subpackage UnitTests
 */
class ObjectStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to object Storage
     *
     * @var ObjectStorage
     */
    protected $objectStorage;


    protected $options;

    public function setUp()
    {
        $this->options = array (
            'url'      => TESTS_ZENDSERVICE_OPENSTACK_AUTHSTORAGE_URL,
            'user'     => TESTS_ZENDSERVICE_OPENSTACK_USER,
            'key'      => TESTS_ZENDSERVICE_OPENSTACK_APIKEY
        );

        $this->nameContainer = 'zf2test';
        $this->metadata = array(
            'foo'  => 'bar',
            'foo2' => 'bar2'
        );

        $http = new HttpClient();
        if (!TESTS_ZENDSERVICE_OPENSTACK_ONLINE) {
            if (!$this->responseExists($this->getName())) {
                $this->markTestSkipped(
                    'The HTTP response is not available, I cannot test ' . $this->getName()
                );
            }
            $httpAdapter = new HttpTest;
            $httpAdapter->setResponse($this->loadResponse('auth'));
            $http->setAdapter($httpAdapter);
        }
        $this->objectStorage = new ObjectStorage($this->options, $http);
        if (!TESTS_ZENDSERVICE_OPENSTACK_ONLINE) {
            $this->objectStorage->getHttpClient()->getAdapter()->setResponse($this->loadResponse($this->getName()));
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

        if (!file_exists($fileResponse) && !empty($this->objectStorage) && $this->objectStorage->isSuccess()) {
            $httpClient = $this->objectStorage->getApiAdapter()->getHttpClient();
            $response   = $httpClient->getResponse()->renderStatusLine() . "\r\n" .
                          $httpClient->getResponse()->getHeaders()->toString() . "\r\n" .
                          $httpClient->getResponse()->getBody();
            file_put_contents($fileResponse, $response);
        }

    }

    public function testListContainers()
    {
        $result = $this->objectStorage->listContainers();
        $this->assertTrue($this->objectStorage->isSuccess());
    }

    public function testCreateContainer()
    {
        $this->assertTrue($this->objectStorage->createContainer($this->nameContainer));
    }

    public function testCreateContainerWithMetadata()
    {
        $this->assertTrue($this->objectStorage->createContainer($this->nameContainer, $this->metadata));
    }

    public function testDeleteContainer()
    {
        $this->assertTrue($this->objectStorage->deleteContainer($this->nameContainer));
    }
}
