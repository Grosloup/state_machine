<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 19:21
 */

namespace Nico;


class Transition implements TransitionInterface
{
    /**
     * @var string
     */
    private $from;
    /**
     * @var string
     */
    private $to;
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $once = true;
    /**
     * @var bool
     */
    private $isDone = false;

    /**
     * @param $object
     */
    public function execute($object)
    {
        if (is_callable($this->callback)) {
            call_user_func_array($this->callback, [&$object]);
        }
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return Transition
     */
    public function setFrom($from = '')
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     * @return Transition
     */
    public function setTo($to = '')
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param mixed $callback
     * @return Transition
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Transition
     */
    public function setName($name = '')
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnce()
    {
        return $this->once;
    }

    /**
     * @param bool $once
     * @return $this
     */
    public function setOnce($once)
    {
        $this->once = $once;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsDone()
    {
        return $this->isDone;
    }

    /**
     * @return mixed
     */
    public function setIsDone()
    {
        $this->isDone = true;

        return $this;
    }


}