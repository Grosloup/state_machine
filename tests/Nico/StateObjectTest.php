<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 12/06/16
 * Time: 12:12
 */

namespace Tests\Nico;


use Nico\StateInterface;
use Nico\StateObject;

class StateObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testHasMethods()
    {
        $so = new StateObject();

        $this->assertInstanceOf(StateInterface::class, $so);
        $this->assertTrue(method_exists($so, 'getState'));
        $this->assertTrue(method_exists($so, 'setState'));
    }
}