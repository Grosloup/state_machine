<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 19:07
 */

namespace Tests\Nico;


use Nico\Collection;
use Nico\StateInterface;
use Nico\StateMachine;
use Nico\StateMachineFactory;
use Nico\StateMachineInterface;
use Nico\StateObject;
use Nico\Transition;

class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    public function testStateMachine()
    {
        $sm = new StateMachine();
        $this->assertInstanceOf(StateMachineInterface::class, $sm);
    }

    public function testMethods()
    {
        $sm = new StateMachine();

        $this->assertTrue(method_exists($sm, 'addState'));
        $this->assertTrue(method_exists($sm, 'can'));
        $this->assertTrue(method_exists($sm, 'apply'));
        $this->assertTrue(method_exists($sm, 'addTransition'));
        $this->assertTrue(method_exists($sm, 'setObject'));
        $this->assertTrue(method_exists($sm, 'getObject'));
        $this->assertTrue(method_exists($sm, 'currentState'));
        $this->assertTrue(method_exists($sm, 'setName'));
        $this->assertTrue(method_exists($sm, 'getName'));
        $this->assertTrue(method_exists($sm, 'setInitialState'));
    }

    public function testCurrentState()
    {
        $object = \Mockery::mock(StateInterface::class);
        $expected = 'proposed';
        $object->shouldReceive('getState')->andReturn($expected);
        $sm = new StateMachine();
        $sm->setObject($object);
        $this->assertEquals($expected, $sm->currentState());
    }

    public function testFactory()
    {
        $smf = new StateMachineFactory();
        $stateObject = new StateObject();
        $stateObject->setState('A');
        $sm = $smf->get($stateObject);

        $this->assertEquals('A', $sm->currentState());
        $this->assertEquals('default', $sm->getName());
    }

    public function testApply()
    {
        $stateObject = new StateObject();
        $stateObject->setState('A');

        $sm = new StateMachine();
        $sm->setObject($stateObject);
        $test = false;
        $transition = new Transition();
        $transition->setName('test')->setFrom('A')->setTo('B')->setCallback(
            function () use (&$test) {
                $test = true;
            }
        );
        $sm->addTransition($transition);
        $sm->apply('test');
        $this->assertTrue($test);
        $this->assertEquals('B', $stateObject->getState());
    }

    public function testApplyWithFactory()
    {
        $stateObject = new StateObject();
        $return = '';
        $config = [
            'test' => [
                'states' => [
                    ['A', StateMachine::STATE_INITIAL],
                    ['B', StateMachine::STATE_NORMAL],
                    ['C', StateMachine::STATE_NORMAL],
                ],
                'transitions' => [
                    [
                        'name' => 'AtoB',
                        'from' => 'A',
                        'to' => 'B',
                        'callback' => function ($obj) use (&$return) {
                            $return = 'A to B';
                        },
                    ],
                    [
                        'name' => 'BtoC',
                        'from' => 'B',
                        'to' => 'C',
                        'callback' => function ($obj) use (&$return) {
                            $return = 'B to C';
                        },
                    ],
                    [
                        'name' => 'CtoA',
                        'from' => 'C',
                        'to' => 'A',
                        'callback' => function ($obj) use (&$return) {
                            $return = 'C to A';
                        },
                    ],
                ],
            ],

        ];

        $smf = new StateMachineFactory($config);
        $smDefault = $smf->get($stateObject, 'default');
        $this->assertNull($smDefault->currentState());
        $this->assertEquals('default', $smDefault->getName());
        $this->assertEquals(0, $smDefault->nextTransitions()->count());


        $sm = $smf->get($stateObject, 'test');
        $this->assertEquals('A', $sm->currentState());
        $this->assertTrue($sm->can('AtoB'));
        $this->assertFalse($sm->can('BtoC'));
        $this->assertFalse($sm->can('CtoA'));

        $sm->apply('CtoA');
        $this->assertEquals('A', $sm->currentState());
        $this->assertEquals('', $return);

        $sm->apply('AtoB');
        $this->assertEquals('B', $sm->currentState());
        $this->assertEquals('A to B', $return);

        $sm->apply('CtoA');
        $this->assertEquals('B', $sm->currentState());
        $this->assertEquals('A to B', $return);

        $sm->apply('BtoC');
        $this->assertEquals('C', $sm->currentState());
        $this->assertEquals('B to C', $return);

        $sm->apply('AtoB');
        $this->assertEquals('C', $sm->currentState());
        $this->assertEquals('B to C', $return);

        $sm->apply('CtoA');
        $this->assertEquals('A', $sm->currentState());
        $this->assertEquals('C to A', $return);

        $transitions = $sm->nextTransitions();
        $this->assertInstanceOf(Collection::class, $transitions);
        $this->assertEquals(1, $transitions->count());
        $this->assertEquals('AtoB', $transitions->first()->getName());
    }

    public function testInitialState()
    {
        $stateObject = new StateObject();
        $return = '';
        $config = [
            'test' => [
                'states' => [
                    ['A', StateMachine::STATE_NORMAL],
                    ['B', StateMachine::STATE_INITIAL],
                    ['C', StateMachine::STATE_NORMAL],
                ],
                'transitions' => [
                    [
                        'name' => 'AtoB',
                        'from' => 'A',
                        'to' => 'B',
                        'callback' => function ($obj) use (&$return) {
                            $return = 'A to B';
                        },
                    ],
                    [
                        'name' => 'BtoC',
                        'from' => 'B',
                        'to' => 'C',
                        'callback' => function ($obj) use (&$return) {
                            $return = 'B to C';
                        },
                    ],
                    [
                        'name' => 'CtoA',
                        'from' => 'C',
                        'to' => 'A',
                        'callback' => function ($obj) use (&$return) {
                            $return = 'C to A';
                        },
                    ],
                ],
            ],

        ];

        $smf = new StateMachineFactory($config);
        $sm = $smf->get($stateObject, 'test');
        $this->assertNotEquals('A', $sm->currentState());

        $this->assertEquals('B', $sm->currentState());
        $sm->apply('CtoA');
        $this->assertEquals('B', $sm->currentState());
        $this->assertEquals('', $return);

        $sm->apply('BtoC');
        $this->assertEquals('C', $sm->currentState());
        $this->assertEquals('B to C', $return);

    }

    public function testCallback()
    {
        $initialTitle = 'initial title';
        $newTitle = 'new title';
        $stateObject = new class extends StateObject
        {
            public $title;
        };
        $stateObject->title = $initialTitle;
        $cbClass = new class
        {
            public $title;

            public function doSomething(StateObject $obj)
            {
                $obj->title = $this->title;
            }
        };
        $cbClass->title = $newTitle;

        $return = '';

        $configs = [
            'test' => [
                'states' => [
                    ['A', StateMachine::STATE_INITIAL],
                    ['B', StateMachine::STATE_NORMAL],
                    ['C', StateMachine::STATE_NORMAL],
                ],
                'transitions' => [
                    [
                        'name' => 'AtoB',
                        'from' => 'A',
                        'to' => 'B',
                        'callback' => [$cbClass, 'doSomething'],
                    ],
                    [
                        'name' => 'BtoC',
                        'from' => 'B',
                        'to' => 'C',
                        'callback' => function ($obj) use (&$return) {
                            $return = 'B to C';
                        },
                    ],
                    [
                        'name' => 'CtoA',
                        'from' => 'C',
                        'to' => 'A',
                        'callback' => function ($obj) use (&$return) {
                            $return = 'C to A';
                        },
                    ],
                ],
            ],
        ];

        $smf = new StateMachineFactory($configs);
        $sm = $smf->get($stateObject, 'test');
        $this->assertEquals('A', $sm->currentState());
        $this->assertTrue($sm->can('AtoB'));
        $this->assertFalse($sm->can('BtoC'));
        $this->assertFalse($sm->can('CtoA'));

        $sm->apply('AtoB');
        $this->assertEquals('B', $sm->currentState());
        $this->assertEquals('', $return);
        $this->assertNotEquals($initialTitle, $stateObject->title);
        $this->assertEquals($newTitle, $stateObject->title);

        $sm->apply('BtoC');
        $this->assertEquals('C', $sm->currentState());
        $this->assertEquals('B to C', $return);
    }

    public function testTransitionExecuteOnce()
    {
        $initialTitle = 'initial title';
        $newTitle = 'new title';
        $stateObject = new class extends StateObject
        {
            public $title;
        };
        $stateObject->title = $initialTitle;
        $cbClass = new class
        {
            public $title;
            public $count = 0;

            public function doSomething(StateObject $obj)
            {
                $obj->title = $this->title;
                $this->count++;
            }
        };
        $cbClass->title = $newTitle;
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
                        'callback' => [$cbClass, 'doSomething'],
                    ],
                ],
            ],
        ];
        $smf = new StateMachineFactory($configs);
        $sm = $smf->get($stateObject, 'test');
        $this->assertEquals('A', $sm->currentState());
        $this->assertTrue($sm->can('AtoA'));
        $this->assertFalse($sm->can('BtoC'));
        $sm->apply('AtoA');
        $this->assertEquals('A', $sm->currentState());
        $this->assertNotEquals($initialTitle, $stateObject->title);
        $this->assertEquals($newTitle, $stateObject->title);
        $this->assertEquals(1, $cbClass->count);
        $sm->apply('AtoA');
        $this->assertEquals(1, $cbClass->count);
    }

    public function testTransitionExecuteMoreThanOnce()
    {
        $initialTitle = 'initial title';
        $newTitle = 'new title';
        $stateObject = new class extends StateObject
        {
            public $title;
        };
        $stateObject->title = $initialTitle;
        $cbClass = new class
        {
            public $title;
            public $count = 0;

            public function doSomething(StateObject $obj)
            {
                $obj->title = $this->title;
                $this->count++;
            }
        };
        $cbClass->title = $newTitle;
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
                        'callback' => [$cbClass, 'doSomething'],
                        'once' => false,
                    ],
                ],
            ],
        ];
        $smf = new StateMachineFactory($configs);
        $sm = $smf->get($stateObject, 'test');
        $this->assertEquals('A', $sm->currentState());
        $this->assertTrue($sm->can('AtoA'));
        $this->assertFalse($sm->can('BtoC'));
        $sm->apply('AtoA');
        $this->assertEquals('A', $sm->currentState());
        $this->assertNotEquals($initialTitle, $stateObject->title);
        $this->assertEquals($newTitle, $stateObject->title);
        $this->assertEquals(1, $cbClass->count);
        $this->assertTrue($sm->can('AtoA'));
        $sm->apply('AtoA');
        $this->assertEquals(2, $cbClass->count);
        $sm->apply('AtoA');
        $this->assertEquals(3, $cbClass->count);
    }
}