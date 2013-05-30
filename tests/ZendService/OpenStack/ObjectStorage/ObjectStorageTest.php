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

    /**
     * Name of the container
     *
     * @var string
     */
    protected $nameContainer;

    /**
     * Name of the object
     *
     * @var string
     */
    protected $nameObject;

    /**
     * Metadata of the container and the object
     *
     * @var string
     */
    protected $metadata;

    /**
     * Content of the object
     *
     * @var string
     */
    protected $contentObject;

    /**
     * Name of the object to copy
     *
     * @var string
     */
    protected $nameCopyObject;

    /**
     * Options for the ObjectStorage
     *
     * @var array
     */
    protected $options;

    /**
     * Copy success
     *
     * @var boolean
     */
    protected static $copySuccess = false;

    public function setUp()
    {
        $this->options = array (
            'url'      => TESTS_ZENDSERVICE_OPENSTACK_AUTHSTORAGE_URL,
            'user'     => TESTS_ZENDSERVICE_OPENSTACK_USER,
            'key'      => TESTS_ZENDSERVICE_OPENSTACK_APIKEY
        );

        $this->nameContainer = 'zf2test';
        $this->nameObject    = 'zf2logo';
        $this->metadata = array(
            'Foo'  => 'bar',
            'Foo2' => 'bar2'
        );
        $this->contentObject = file_get_contents(__DIR__ . '/_files/zf2_logo.png');
        $this->nameCopyObject = $this->nameObject . '-copy';

        $http = new HttpClient();
        // Use this for SSL certificates on Debian Linux box
        // $http = new HttpClient(null, array('sslcapath' => '/etc/ssl/certs'));

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

    public function testCreateContainer()
    {
        $this->assertTrue($this->objectStorage->createContainer($this->nameContainer));
    }

    public function testSetContainerMetadata()
    {
        $result = $this->objectStorage->setContainerMetadata($this->nameContainer, $this->metadata);
        $this->assertTrue($result);
    }

    public function testGetContainerMetadata()
    {
        $metadata = $this->objectStorage->getContainerMetadata($this->nameContainer);
        $this->assertEquals($this->metadata, $metadata['metadata']);
    }

    public function testDeleteContainerMetadata()
    {
        $result = $this->objectStorage->deleteContainerMetadata($this->nameContainer, array_keys($this->metadata));
        $this->assertTrue($result);
        $metadata = $this->objectStorage->getContainerMetadata($this->nameContainer);
        $this->assertTrue(!isset($metadata['metadata']));
    }

    public function testListContainers()
    {
        $result = $this->objectStorage->listContainers();
        $this->assertTrue($this->objectStorage->isSuccess());
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) >= 1);
    }


    public function testCreateContainerWithMetadata()
    {
        $this->assertTrue($this->objectStorage->createContainer($this->nameContainer, $this->metadata));
    }

    public function testSetObject()
    {
        $result = $this->objectStorage->setObject($this->nameContainer, $this->nameObject, $this->contentObject);
        $this->assertTrue($result);
    }

    public function testGetObject()
    {
        $result = $this->objectStorage->getObject($this->nameContainer, $this->nameObject);
        $this->assertEquals($result, $this->contentObject);
    }

    public function testSetObjectMetadata()
    {
        $result = $this->objectStorage->setObjectMetadata($this->nameContainer, $this->nameObject, $this->metadata);
        $this->assertTrue($result);
    }

    public function testGetObjectMetadata()
    {
        $metadata = $this->objectStorage->getObjectMetadata($this->nameContainer, $this->nameObject);
        $this->assertEquals($metadata['metadata'], $this->metadata);
    }

    public function testCopyObject()
    {
        self::$copySuccess = $this->objectStorage->copyObject(
            $this->nameContainer,
            $this->nameObject,
            $this->nameContainer,
            $this->nameCopyObject
        );
        $this->assertTrue(self::$copySuccess);
    }

    public function testDeleteObject()
    {
        $this->assertTrue($this->objectStorage->deleteObject(
            $this->nameContainer,
            $this->nameObject
        ));
        if (self::$copySuccess) {
            $this->assertTrue($this->objectStorage->deleteObject(
                $this->nameContainer, $this->nameCopyObject
            ));
        }
    }

    public function testDeleteContainer()
    {
        $this->assertTrue($this->objectStorage->deleteContainer($this->nameContainer));
    }
}
