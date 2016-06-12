<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 20:35
 */

namespace Nico;


interface StateInterface
{
    public function setState($state);

    public function getState();
}