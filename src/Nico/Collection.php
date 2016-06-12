<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 19:39
 */

namespace Nico;

class Collection implements \IteratorAggregate, CollectionInterface
{
    /**
     * @var array
     */
    private $elements = [];

    /**
     * Collection constructor.
     * @param array $elements
     */
    public function __construct(array $elements = null)
    {
        $this->elements = $elements === null ? [] : $elements;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @param $element
     * @return bool
     */
    public function contains($element)
    {
        return in_array($element, $this->elements, true);
    }

    /**
     * @param $element
     * @return $this
     */
    public function add($element)
    {
        $this->elements[] = $element;

        return $this;
    }

    /**
     * @param $element
     * @return bool
     */
    public function removeElement($element)
    {
        $key = array_search($element, $this->elements, true);
        if ($key === false) {
            return false;
        }
        unset($this->elements[$key]);

        return true;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    public function containsKey($key)
    {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    /**
     * @param mixed $offset
     * @return array|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return isset($this->elements[$key]) ? $this->elements[$key] : $default;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param $key
     * @param $element
     * @return $this
     */
    public function set($key, $element)
    {
        $this->elements[$key] = $element;

        return $this;
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function remove($key)
    {
        if (!isset($this->elements[$key]) && !array_key_exists($key, $this->elements)) {
            return null;
        }
        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->elements);
    }

    /**
     * @param $element
     * @return int
     */
    public function push($element)
    {
        return array_push($this->elements, $element);
    }

    /**
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->elements);
    }

    /**
     * @param $element
     * @return int
     */
    public function unshift($element)
    {
        return array_unshift($this->elements, $element);
    }

    /**
     * @return mixed|false
     */
    public function last()
    {
        return end($this->elements);
    }

    /**
     * @return mixed|false
     */
    public function first()
    {
        return reset($this->elements);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->elements);
    }

    /**
     * @return mixed
     */
    public function next()
    {
        return next($this->elements);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * @return mixed
     */
    public function prev()
    {
        return prev($this->elements);
    }

    /**
     * @param callable $callback
     * @return Collection
     */
    public function filter($callback)
    {
        return new self(array_filter($this->elements, $callback));
    }
}