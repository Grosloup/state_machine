<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 12/06/16
 * Time: 12:15
 */

namespace Tests\Nico;


use Nico\StateMachine;
use Nico\StateMachineFactory;

class StateMachineFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigs()
    {
        $configs = [
            'test' => [
                'states' => [
                    ['A', StateMachine::STATE_INITIAL],
                    ['B', StateMachine::STATE_NORMAL],
                ],
                'transitions' => [
                    [
                        'name' => 'AtoA',
                        'from' => 'A',
                        'to' => 'A',
                        'callback' => function ($obj) {
                        },
                        'once' => false,
                    ],
                ],
            ],
        ];
        $smf = new StateMachineFactory($configs);

        $configs = $smf->getConfigs();

        $this->assertTrue(array_key_exists('default', $configs));
        $this->assertTrue(array_key_exists('test', $configs));
    }
}