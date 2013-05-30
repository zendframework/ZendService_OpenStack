<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\OpenStack\Compute;

use ArrayAccess;
use Countable;
use Iterator;
use ZendService\OpenStack\Servers as OpenStackServers;

/**
 * List of shared Ip group of OpenStack
 */
class SharedIpGroupList implements Countable, Iterator, ArrayAccess
{
    /**
     * @var OpenStackServers\SharedIpGroup[]
     */
    protected $shared = array();

    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;

    /**
     * @var OpenStackServers
     */
    protected $service;

    /**
     * @param  OpenStackServers $service
     * @param  array $list
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(OpenStackServers $service, $list = array())
    {
        if (!($service instanceof OpenStackServers) || !is_array($list)) {
            throw new Exception\InvalidArgumentException("You must pass a ZendService\OpenStack\Servers object and an array");
        }

        $this->service= $service;
        $this->constructFromArray($list);
    }

    /**
     * Transforms the array to array of Shared Ip Group
     *
     * @param  array $list
     */
    protected function constructFromArray(array $list)
    {
        foreach ($list as $share) {
            $this->addSharedIpGroup(new SharedIpGroup($this->service,$share));
        }
    }

    /**
     * Add a shared Ip group
     *
     * @param  OpenStackServers\SharedIpGroup $share
     * @return OpenStackServers\SharedIpGroupList
     */
    protected function addSharedIpGroup (SharedIpGroup $share)
    {
        $this->shared[] = $share;
        return $this;
    }

    /**
     * To Array
     *
     * @return array
     */
    public function toArray()
    {
        $array= array();
        foreach ($this->shared as $share) {
            $array[]= $share->toArray();
        }
        return $array;
    }

    /**
     * Return number of shared Ip Groups
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->shared);
    }

    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return OpenStackServers\SharedIpGroup
     */
    public function current()
    {
        return $this->shared[$this->iteratorKey];
    }

    /**
     * Return the key of the current element
     *
     * Implement Iterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->iteratorKey;
    }

    /**
     * Move forward to next element
     *
     * Implement Iterator::next()
     */
    public function next()
    {
        $this->iteratorKey += 1;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * Implement Iterator::rewind()
     */
    public function rewind()
    {
        $this->iteratorKey = 0;
    }

    /**
     * Check if there is a current element after calls to rewind() or next()
     *
     * Implement Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        $numItems = $this->count();
        if ($numItems > 0 && $this->iteratorKey < $numItems) {
            return true;
        }

        return false;
    }

    /**
     * Whether the offset exists
     *
     * Implement ArrayAccess::offsetExists()
     *
     * @param  int     $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return ($offset < $this->count());
    }

    /**
     * Return value at given offset
     *
     * Implement ArrayAccess::offsetGet()
     *
     * @param  int  $offset
     * @return OpenStackServers\SharedIpGroup
     * @throws Exception\OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
        return $this->shared[$offset];
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param  int     $offset
     * @param  string  $value
     * @throws Exception\RuntimeException
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception\RuntimeException('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param  int $offset
     * @throws Exception\RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new Exception\RuntimeException('You are trying to unset read-only property');
    }
}
