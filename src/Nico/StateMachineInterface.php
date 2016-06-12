<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 19:05
 */

namespace Nico;


interface StateMachineInterface
{
    public function addState($state);

    public function can($transitionName = '');

    public function apply($transitionName = '');

    public function addTransition(TransitionInterface $transition);

    public function setObject(StateInterface $object);

    public function getObject();

    public function currentState();

    public function nextTransitions();

    public function setName($name = 'default');

    public function getName();
}