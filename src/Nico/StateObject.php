<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 12/06/16
 * Time: 07:18
 */

namespace Nico;


class StateObject implements StateInterface
{
    /**
     * @var string
     */
    private $state;

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }
}