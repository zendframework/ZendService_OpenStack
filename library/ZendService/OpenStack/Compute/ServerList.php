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
use ZendService\OpenStack\Compute;

/**
 * List of servers of OpenStack
 */
class ServerList implements Countable, Iterator, ArrayAccess
{
    /**
     * @var array of Server
     */
    protected $servers = array();

    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;

    /**
     * @var Compute
     */
    protected $service;

    /**
     * Construct
     *
     * @param  Compute $service
     * @param  array $list
     */
    public function __construct(Compute $service, array $list = array())
    {
        $this->service = $service;
        $this->constructFromArray($list);
    }

    /**
     * Transforms the array to array of Server
     *
     * @param  array $list
     * @return void
     */
    protected function constructFromArray(array $list)
    {
        foreach ($list as $server) {
            $this->addServer(new Server($this->service,$server));
        }
    }

    /**
     * Add a server
     *
     * @param  Server $server
     * @return ServerList
     */
    protected function addServer(Server $server)
    {
        $this->servers[] = $server;
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
        foreach ($this->servers as $server) {
            $array[]= $server->toArray();
        }
        return $array;
    }

    /**
     * Return number of servers
     *
     * Implement Countable::count()
     *
     * @return integer
     */
    public function count()
    {
        return count($this->servers);
    }

    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return Server
     */
    public function current()
    {
        return $this->servers[$this->iteratorKey];
    }

    /**
     * Return the key of the current element
     *
     * Implement Iterator::key()
     *
     * @return integer
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
        } else {
            return false;
        }
    }

    /**
     * Whether the offset exists
     *
     * Implement ArrayAccess::offsetExists()
     *
     * @param   integer     $offset
     * @return  bool
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
     * @param   integer     $offset
     * @return  Server
     * @throws  Exception\OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->servers[$offset];
        } else {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   integer     $offset
     * @param   string  $value
     * @throws  Exception\RuntimeException
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
     * @param   integer     $offset
     * @throws  Exception\RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new Exception\RuntimeException('You are trying to unset read-only property');
    }
}
