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
 * List of images of OpenStack
 */
class ImageList implements Countable, Iterator, ArrayAccess
{
    /**
     * @var array of \ZendService\OpenStack\Servers\Image
     */
    protected $images = array();

    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;

    /**
     * @var \ZendService\OpenStack\Servers
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
     */
    protected function constructFromArray(array $list)
    {
        foreach ($list as $image) {
            $this->addImage(new Image($this->service,$image));
        }
    }

    /**
     * Add an image
     *
     * @param  Image $image
     * @return ImageList
     */
    protected function addImage (Image $image)
    {
        $this->images[] = $image;
        return $this;
    }

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
    {
        $array= array();
        foreach ($this->images as $image) {
            $array[] = $image->toArray();
        }
        return $array;
    }

    /**
     * Return number of images
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->images);
    }

    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return Image
     */
    public function current()
    {
        return $this->images[$this->iteratorKey];
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
     * @param   int     $offset
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
     * @param   int     $offset
     * @return  Image
     * @throws  Exception\OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
        return $this->images[$offset];
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  Exceptio\RuntimeExceptionn
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
     * @param   int     $offset
     * @throws  Exception\RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new Exception\RuntimeException('You are trying to unset read-only property');
    }
}
