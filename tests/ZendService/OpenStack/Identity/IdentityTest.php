<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendServiceTest\OpenStack\Identity;

use ZendService\OpenStack\Identity;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @subpackage UnitTests
 */
class IdentityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Identity
     *
     * @var Identity
     */
    protected $identity;

    protected static $token;

    protected static $userId;

    protected static $tenantId;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->options = array(
            'url'      => TESTS_ZENDSERVICE_OPENSTACK_URL,
            'user'     => TESTS_ZENDSERVICE_OPENSTACK_USER,
            'password' => TESTS_ZENDSERVICE_OPENSTACK_PASSWORD,
            'key'      => TESTS_ZENDSERVICE_OPENSTACK_APIKEY,
        );

        $http = new HttpClient();

        if (!TESTS_ZENDSERVICE_OPENSTACK_ONLINE) {
            if (!$this->responseExists($this->getName())) {
                $this->markTestSkipped(
                    'I cannot find the ' . $this->getName() . '.response file'
                );
            }
            $httpAdapter = new HttpTest;
            $httpAdapter->setResponse($this->loadResponse('testAuthenticate'));
            $http->setAdapter($httpAdapter);
        }
        $this->identity = new Identity($this->options, $http);
        if (!TESTS_ZENDSERVICE_OPENSTACK_ONLINE) {
            $this->identity->getHttpClient()->getAdapter()->setResponse($this->loadResponse($this->getName()));
        }
    }

    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
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
        $fileResponse = __DIR__ . '/_files/' . $this->getName() . '.response';
        if (!file_exists($fileResponse) && !empty($this->identity) && $this->identity->isSuccess()) {
            $httpClient = $this->identity->getApiAdapter()->getHttpClient();
            $response   = $httpClient->getResponse()->renderStatusLine() . "\r\n" .
                          $httpClient->getResponse()->getHeaders()->toString() . "\r\n" .
                          $httpClient->getResponse()->getBody();
            file_put_contents($fileResponse, $response);
        }
    }

    public function testAuthenticate()
    {
        $result = $this->identity->authenticate($this->options['user'], $this->options['password']);

        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
        self::$token = $this->identity->getToken();
        $this->assertTrue(!empty(self::$token));
    }

    public function testAuthenticateByKey()
    {
        $result = $this->identity->authenticateByKey($this->options['user'], $this->options['key']);

        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
        self::$token = $this->identity->getToken();
        $this->assertTrue(!empty(self::$token));
    }

    public function testValidateToken()
    {
        $result = $this->identity->validateToken(self::$token);
        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
    }

    public function testCheckToken()
    {
        $result = $this->identity->validateToken(self::$token);
        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
    }

    public function testListEndpointsToken()
    {
        $result = $this->identity->listEndpointsToken(self::$token);
        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
    }

    public function testListUsers()
    {
        $result = $this->identity->listUsers();
        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
    }

    public function testAddUser()
    {
        $user = array (
            'username' => 'zf2test',
            'email'    => 'test@test.com',
            'password' => 'ZendFramework2'
        );
        $result = $this->identity->addUser($user);
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result['user']['id']));
        self::$userId = $result['user']['id'];
        $this->assertTrue($this->identity->isSuccess());
    }

    public function testUpdateUser()
    {
        if (empty(self::$userId)) {
            $this->markTestSkipped('The user ID is empty');
        }
        $user = array (
            'id'       => self::$userId,
            'username' => 'zf2testChanged',
            'email'    => 'test@test.com'
        );
        $result = $this->identity->updateUser($user);
        $this->assertTrue(is_array($result));
        $this->assertEquals(self::$userId, $result['user']['id']);
        $this->assertEquals($user['username'], $result['user']['username']);
        $this->assertTrue($this->identity->isSuccess());
    }

    public function testDeleteUser()
    {
        if (empty(self::$userId)) {
            $this->markTestSkipped('The user ID is empty');
        }
        $result = $this->identity->deleteUser(self::$userId);
        $this->assertTrue($result);
        $this->assertTrue($this->identity->isSuccess());
    }

    public function testListGlobalRoles()
    {
        if (empty(self::$userId)) {
            $this->markTestSkipped('The user ID is empty');
        }
        $result = $this->identity->listGlobalRoles(self::$userId);
        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
    }

    public function testAddTenant()
    {
        $tenant = array(
            'name'        => 'test',
            'description' => 'this is a test tenant'
        );
        $result = $this->identity->addTenant($tenant);
        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
        $this->assertEquals($tenant['name'], $result['tenant']['name']);
        $this->assertEquals($tenant['description'], $result['tenant']['description']);
        self::$tenantId = $result['tenant']['id'];
    }

    public function testUpdateTenant()
    {
        if (empty(self::$tenantId)) {
            $this->markTestSkipped('The tenant ID is empty');
        }
        $tenant = array(
            'id'          => self::$tenantId,
            'name'        => 'test2',
            'description' => 'this is a test tenant'
        );
        $result = $this->identity->updateTenant($tenant);
        $this->assertTrue(is_array($result));
        $this->assertTrue($this->identity->isSuccess());
        $this->assertEquals($tenant['name'], $result['tenant']['name']);
        $this->assertEquals($tenant['description'], $result['tenant']['description']);
    }

    public function testDeleteTenant()
    {
        if (empty(self::$tenantId)) {
            $this->markTestSkipped('The tenant ID is empty');
        }
        $result = $this->identity->deleteTenant(self::$tenantId);
        $this->assertTrue($result);
        $this->assertTrue($this->identity->isSuccess());
    }
}
