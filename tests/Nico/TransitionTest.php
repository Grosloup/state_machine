<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/06/16
 * Time: 19:20
 */

namespace Tests\Nico;


use Nico\Transition;
use Nico\TransitionInterface;

class TransitionTest extends \PHPUnit_Framework_TestCase
{
    public function testTransition()
    {
        $transition = new Transition();
        $this->assertInstanceOf(TransitionInterface::class, $transition);
    }

    public function testExecute()
    {
        $transition = new Transition();
        $test = false;
        $obj = new \stdClass();
        $obj->isTested = false;
        $transition->setFrom('A')->setTo('B')->setCallback(
            function ($object) use (&$test) {
                $test = true;
                $object->isTested = true;
            }
        );

        $transition->execute($obj);
        $this->assertTrue($test);
        $this->assertTrue($obj->isTested);
    }


}