<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 19:34
 */

namespace Nico;


interface CollectionInterface extends \Traversable, \Countable, \ArrayAccess
{
    public function contains($element);

    public function remove($key);

    public function removeElement($element);

    public function containsKey($key);

    public function get($key, $default = null);

    public function set($key, $value);

    public function add($element);

    public function pop();

    public function push($element);

    public function shift();

    public function unshift($element);

    public function last();

    public function first();

    public function key();

    public function current();

    public function next();

    public function prev();

    public function filter($callback);
}