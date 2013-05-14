<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendService\OpenStack\Compute;

use ZendService\OpenStack\Compute;

/**
 * List of images of OpenStack
 *
 * @category   Zend
 * @package    ZendService\OpenStack
 * @subpackage Servers
 */
class ImageList implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array of ZendService\OpenStack\Servers\Image
     */
    protected $images = array();
    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;
    /**
     * @var ZendService\OpenStack\Servers
     */
    protected $service;
    /**
     * Construct
     *
     * @param  Compute $service
     * @param  array $list
     * @return void
     */
    public function __construct(Compute $service, array $list = array())
    {
        $this->service= $service;
        $this->constructFromArray($list);
    }
    /**
     * Transforms the array to array of Server
     *
     * @param  array $list
     * @return void
     */
    private function constructFromArray(array $list)
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
     * To Array
     *
     * @return array
     */
    public function toArray()
    {
        $array= array();
        foreach ($this->images as $image) {
            $array[]= $image->toArray();
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
     *
     * @return void
     */
    public function next()
    {
        $this->iteratorKey += 1;
    }
    /**
     * Rewind the Iterator to the first element
     *
     * Implement Iterator::rewind()
     *
     * @return void
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
     * @return boolean
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
     * @param   int     $offset
     * @return  boolean
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
     * @throws  Exception\OutOfBoundsException
     * @return  Image
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->images[$offset];
        } else {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     * @throws  Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception('You are trying to unset read-only property');
    }
}
