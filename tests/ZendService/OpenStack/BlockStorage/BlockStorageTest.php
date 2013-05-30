<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendServiceTest\OpenStack\Compute;

use ZendService\OpenStack\BlockStorage;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @subpackage UnitTests
 */
class BlockStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to blockStorage object
     *
     * @var Networking
     */
    protected $blockStorage;

    public function setUp()
    {
        $this->options = array (
            'url'      => TESTS_ZENDSERVICE_OPENSTACK_URL,
            'user'     => TESTS_ZENDSERVICE_OPENSTACK_USER,
            'password' => TESTS_ZENDSERVICE_OPENSTACK_PASSWORD,
            'key'      => TESTS_ZENDSERVICE_OPENSTACK_APIKEY
        );

        $this->volumeId = '5aa119a8-d25b-45a7-8d1b-88e127885635';
        $this->snapshotId = '2bb856e1-b3d8-4432-a858-09e4ce939389';
        $this->volumeTypeId = '6685584b-1eac-4da6-b5c3-555430cf68ff';

        if (!$this->responseExists($this->getName())) {
            $this->markTestSkipped(
                'I cannot find the ' . $this->getName() . '.response file'
            );
        }
        $httpAdapter = new HttpTest;
        $httpAdapter->setResponse($this->loadResponse('../../Identity/_files/testAuthenticate'));
        $http = new HttpClient;
        $http->setAdapter($httpAdapter);
        $this->blockStorage = new BlockStorage($this->options, $http);
        $this->blockStorage->getHttpClient()->getAdapter()->setResponse($this->loadResponse($this->getName()));
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

    public function testCreateVolume()
    {
        $volume = array (
            'name' => 'vol-001',
            'description' => 'Another volume.',
            'size' => 30,
            'volume_type' => '289da7f8-6440-407c-9fb4-7db01ec49164',
            'availability_zone' => 'us-east1',
            'metadata' => array('contents' => 'junk'),
        );
        $result = $this->blockStorage->createVolume($volume);
        $this->assertTrue($this->blockStorage->isSuccess());
    }

    public function testListVolume()
    {
        $result = $this->blockStorage->listVolume();
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result['volumes']));
        foreach ($result['volumes'] as $vol) {
            $this->assertTrue(isset($vol['name']));
            $this->assertTrue(isset($vol['id']));
        }
    }

    public function testListVolumeDetails()
    {
        $result = $this->blockStorage->listVolume(true);
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result['volumes']));
        foreach ($result['volumes'] as $vol) {
            $this->assertTrue(isset($vol['name']));
            $this->assertTrue(isset($vol['id']));
            $this->assertTrue(isset($vol['status']));
        }
    }

    public function testShowVolume()
    {
        $volume = $this->blockStorage->showVolume($this->volumeId);
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(isset($volume['volume']));
        $this->assertEquals($this->volumeId, $volume['volume']['id']);
    }

    public function testUpdateVolume()
    {
        $options = array(
            'name' => 'vol-003',
            'description' => 'This is yet, another volume.'
        );
        $volume = $this->blockStorage->updateVolume($this->volumeId, $options);
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(isset($volume['volume']));
        $this->assertEquals($options['name'], $volume['volume']['name']);

    }

    public function testDeleteVolume()
    {
        $this->assertTrue($this->blockStorage->deleteVolume($this->volumeId));
        $this->assertTrue($this->blockStorage->isSuccess());
    }

    public function testCreateSnapshot()
    {
        $options = array(
            'name'        => 'snap-001',
            'description' => 'Daily backup',
            'volume_id'   => '5aa119a8-d25b-45a7-8d1b-88e127885635',
            'force'       => 'true'
        );
        $snapshot = $this->blockStorage->createSnapshot($options);
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(isset($snapshot['snapshot']));
        $this->assertEquals($options['name'], $snapshot['snapshot']['name']);
    }

    public function testListSnapshot()
    {
        $snapshots = $this->blockStorage->listSnapshot();
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(is_array($snapshots));
        $this->assertTrue(isset($snapshots['snapshots']));
        $this->assertEquals(2, count($snapshots['snapshots']));
    }

    public function testListSnapshotDetails()
    {
        $snapshots = $this->blockStorage->listSnapshot(true);
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(isset($snapshots['snapshots']));
        $this->assertEquals(2, count($snapshots['snapshots']));
        $this->assertTrue(isset($snapshots['snapshots'][0]['os-extended-snapshot-attributes:progress']));
    }

    public function testShowSnapshot()
    {
        $snapshot = $this->blockStorage->showSnapshot($this->snapshotId);
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(isset($snapshot['snapshot']));
        $this->assertEquals($this->snapshotId, $snapshot['snapshot']['id']);
    }

    public function testUpdateSnapshot()
    {
        $options = array(
            'name' => 'snap-002',
            'description' => 'This is yet, another snapshot'
        );
        $snapshot = $this->blockStorage->updateSnapshot($this->snapshotId, $options);
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(isset($snapshot['snapshot']));
        $this->assertEquals($options['description'], $snapshot['snapshot']['description']);
    }

    public function testDeleteSnapshot()
    {
        $this->assertTrue($this->blockStorage->deleteSnapshot($this->snapshotId));
    }

    public function testListVolumeType()
    {
        $types = $this->blockStorage->listVolumeType();
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(is_array($types));
        $this->assertTrue(isset($types['volume_types']));
        $this->assertEquals(2, count($types['volume_types']));
    }

    public function testShowVolumeType()
    {
        $type = $this->blockStorage->showVolumeType($this->volumeTypeId);
        $this->assertTrue($this->blockStorage->isSuccess());
        $this->assertTrue(isset($type['volume_type']));
        $this->assertEquals($this->volumeTypeId, $type['volume_type']['id']);
    }

}
