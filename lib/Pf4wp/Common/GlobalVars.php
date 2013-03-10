<?php

/*
 * Copyright (c) 2011-2013 Mike Green <myatus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pf4wp\Common;

/**
 * Class providing a pf4wp wide (global) variable storage
 *
 * @package Pf4wp
 * @subpackage Globals
 * @api
 * @since 1.1
 */
class GlobalVars implements \ArrayAccess, \Countable, \Serializable, \IteratorAggregate
{
    static private $globals = array();

    /**
     * Exports globals to an array
     *
     * @return array
     * @api
     */
    public function getArrayCopy()
    {
        return self::$globals;
    }

    /**
     * Serializes the globals
     *
     * @return string
     * @api
     */
    public function serialize() {
        return serialize(self::$globals);
    }

    /**
     * Unserializes a serialized string
     *
     * @param string Serialized string
     * @api
     */
    public function unserialize($serialized)
    {
        self::$globals = unserialize($serialized);
    }

    /**
     * Provides an iterator for global array
     *
     * @return ArrayIterator
     * @api
     */
    public function getIterator() {
        return new \ArrayIterator(self::$globals);
    }

    /**
     * Provides the count of the array
     *
     * @return int
     * @api
     */
    public function count()
    {
        return count(self::$globals);
    }

    /**
     * Sets an global value at the provided offset
     *
     * @param mixed $offset The offset at which to set the value
     * @param mixed $value The value to set
     * @api
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            self::$globals[] = $value;
        } else {
            self::$globals[$offset] = $value;
        }
    }

    /**
     * Tests if the provided offset exists in the globals
     *
     * @param mixed $offset The offset to test
     * @return bool
     * @api
     */
    public function offsetExists($offset)
    {
        return isset(self::$globals[$offset]);
    }

    /**
     * Clears the value and removes the specified offset in the array
     *
     * @param mixed $offset The offest to remove
     * @api
     */
    public function offsetUnset($offset)
    {
        unset(self::$globals[$offset]);
    }

    /**
     * Retrieves the value at the specified offset
     *
     * @param mixed $offset The offset at which to retrieve the value
     * @return mixed
     * @api
     */
    public function offsetGet($offset)
    {
        return isset(self::$globals[$offset]) ? self::$globals[$offset] : null;
    }

    /**
     * Magic for setting a value
     *
     * @api
     */
    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    /**
     * Magic for getting a value
     *
     * @api
     */
    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * Magic for testing a value
     *
     * @api
     */
    public function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    /**
     * Magic for unsetting a value
     *
     * @api
     */
    public function __unset($offset)
    {
        $this->offsetUnset($offset);
    }
}