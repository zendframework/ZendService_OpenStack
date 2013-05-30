<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendServiceTest\OpenStack\BlockStorage\Extensions;

use ZendService\OpenStack\BlockStorage\Extensions\Backup;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @subpackage UnitTests
 */
class BackupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Backup object
     *
     * @var Backup
     */
    protected $backup;

    /**
     * @var string
     */
    protected $backupId;

    /**
     * @var string
     */
    protected $volumeId;

    public function setUp()
    {
        $this->options = array(
            'url'      => 'http://identity.api.openstack.com',
            'user'     => 'test',
            'password' => 'test',
            'key'      => '123cfe4c13a3e321d609c402cd43f936a'
        );

        $this->backupId = '2ef47aee-8844-490c-804d-2a8efe561c65';
        $this->volumeId = '795114e8-7489-40be-a978-83797f2c1dd3';

        if (!$this->responseExists($this->getName())) {
            $this->markTestSkipped(
                'I cannot find the ' . $this->getName() . '.response file'
            );
        }
        $httpAdapter = new HttpTest;
        $httpAdapter->setResponse($this->loadResponse('../../../Identity/_files/testAuthenticate'));
        $http = new HttpClient;
        $http->setAdapter($httpAdapter);
        $this->backup = new Backup($this->options, $http);
        $this->backup->getHttpClient()->getAdapter()->setResponse($this->loadResponse($this->getName()));
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

    public function testCreateBackup()
    {
        $options = array(
            'container'   => null,
            'description' => null,
            'name'        => 'backup001',
            'volume_id'   => '64f5d2fb-d836-4063-b7e2-544d5c1ff607'
        );
        $result = $this->backup->createBackup($options);
        $this->assertTrue($this->backup->isSuccess());
        $this->assertTrue(is_array($result));
        $this->assertEquals($options['name'], $result['backup']['name']);
    }

    public function testListBackup()
    {
        $result = $this->backup->listBackup();
        $this->assertTrue($this->backup->isSuccess());
        $this->assertTrue(is_array($result));
    }

    public function testListBackupDetails()
    {
        $result = $this->backup->listBackup(true);
        $this->assertTrue($this->backup->isSuccess());
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result['backups'][0]['availability_zone']));

    }

    public function testShowBackup()
    {
        $result = $this->backup->showBackup($this->backupId);
        $this->assertTrue($this->backup->isSuccess());
        $this->assertTrue(is_array($result));
        $this->assertEquals($this->backupId, $result['backup']['id']);

    }

    public function testDeleteBackup()
    {
        $this->assertTrue($this->backup->deleteBackup($this->backupId));
    }

    public function testRestoreBackup()
    {
        $result = $this->backup->restoreBackup($this->backupId, $this->volumeId);
        $this->assertTrue($this->backup->isSuccess());
        $this->assertTrue(is_array($result));
        $this->assertEquals($this->backupId, $result['restore']['backup_id']);
        $this->assertEquals($this->volumeId, $result['restore']['volume_id']);
    }
}
