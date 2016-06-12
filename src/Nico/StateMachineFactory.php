<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 12/06/16
 * Time: 08:02
 */

namespace Nico;


class StateMachineFactory
{
    /**
     * @var array
     */
    private $configs;

    /**
     * StateMachineFactory constructor.
     * @param array $configs
     */
    public function __construct($configs = [])
    {
        $this->configs = array_merge(
            [
                'default' => [
                    'states' => [],
                    'transitions' => [],
                ],
            ],
            $configs
        );
    }

    /**
     * @param StateObject $object
     * @param string $name
     * @return StateMachine|null
     */
    public function get(StateObject $object, $name = 'default')
    {
        if (isset($this->configs[$name])) {
            $configs = $this->configs[$name];
            $sm = new StateMachine();
            $sm->setName($name);
            foreach ($configs['states'] as $state) {
                $sm->addState($state);
            }
            foreach ($configs['transitions'] as $key => $transition) {
                $t = new Transition();
                $t->setName(!empty($transition['name']) ? $transition['name'] : 'transition_'.$key);
                foreach ($transition as $k => $value) {
                    if (($k === 'name')) {
                        continue;
                    }
                    $method = 'set'.ucfirst($k);
                    if (method_exists($t, $method)) {
                        $t->$method($value);
                    }
                }
                $sm->addTransition($t);
            }
            $sm->setObject($object);

            return $sm;
        }

        return null;
    }

    public function getConfigs()
    {
        return $this->configs;
    }
}