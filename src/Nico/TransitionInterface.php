<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 19:23
 */

namespace Nico;


interface TransitionInterface
{
    public function getFrom();

    public function setFrom($from = '');

    public function getTo();

    public function setTo($to = '');

    public function getCallback();

    public function setCallback($callback);

    public function getName();

    public function setName($name = '');

    public function execute($object);

    public function setOnce($once);

    public function getOnce();

    public function setIsDone();

    public function getIsDone();
}