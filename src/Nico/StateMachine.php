<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 19:05
 */

namespace Nico;


class StateMachine implements StateMachineInterface
{
    const STATE_INITIAL = 0;
    const STATE_NORMAL = 1;
    const STATE_FINAL = 2;
    /**
     * @var Collection
     */
    private $states;
    /**
     * @var Collection
     */
    private $transitions;
    /**
     * @var StateInterface
     */
    private $object;
    /**
     * @var string
     */
    private $name;

    /**
     * StateMachine constructor.
     */
    public function __construct()
    {
        $this->states = new Collection();
        $this->transitions = new Collection();
    }

    /**
     * @param string|array $state
     * @return $this
     */
    public function addState($state)
    {
        if (!is_array($state)) {
            $state = (array)$state;
            $state[] = self::STATE_NORMAL;
        }
        $this->states->add($state);

        return $this;
    }

    /**
     * @param string $transitionName
     */
    public function apply($transitionName = '')
    {
        if (!$transitionName) {
            return;
        }
        /** @var Transition $transition */
        if (false === $transition = $this->can($transitionName, true)) {
            return;
        }
        if ($transition->getOnce() && $transition->getIsDone()) {
            return;
        }
        $transition->execute($this->object);
        $transition->setIsDone();
        $this->object->setState($transition->getTo());
    }

    /**
     * @param string $transitionName
     * @param bool $return
     * @return bool|false|mixed
     */
    public function can($transitionName = '', $return = false)
    {
        $state = $this->object->getState();
        $transitions = $this->transitions->filter(
            function (Transition $t) use ($transitionName, $state) {
                return $t->getName() === $transitionName && $t->getFrom() === $state;
            }
        );
        $test = $transitions->first();

        return $test === false ? false : $return === false ? true : $test;
    }

    /**
     * @param TransitionInterface $transition
     * @return $this
     */
    public function addTransition(TransitionInterface $transition)
    {
        $this->transitions->add($transition);

        return $this;
    }

    /**
     * @return StateInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param StateInterface $object
     * @return $this
     */
    public function setObject(StateInterface $object)
    {
        $this->object = $object;
        if ($this->object->getState() == null) {
            $this->setInitialState();
        }

        return $this;
    }

    /**
     *
     */
    private function setInitialState()
    {
        $initialStateCollection = $this->states->filter(
            function ($state) {
                return $state[1] === StateMachine::STATE_INITIAL;
            }
        );
        if ($initialStateCollection->count() > 0) {
            $this->object->setState($initialStateCollection->first()[0]);
        } else {
            if ($this->states->first()[1] !== self::STATE_FINAL) {
                $this->object->setState($this->states->first()[0]);
            } else {
                $this->object->setState($this->states->next()[0]);
            }
        }
    }

    /**
     * @return string
     */
    public function currentState()
    {
        return $this->object->getState();
    }

    /**
     * @return Collection
     */
    public function nextTransitions()
    {
        $state = $this->object->getState();

        return $this->transitions->filter(
            function (Transition $t) use ($state) {
                return $t->getFrom() === $state;
            }
        );
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name = 'default')
    {
        $this->name = $name;

        return $this;
    }
}