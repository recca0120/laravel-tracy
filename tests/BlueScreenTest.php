<?php

use Mockery as m;
use Recca0120\LaravelTracy\BlueScreen;

class BlueScreenTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_render()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $blueScreen = new BlueScreen();
        $exception = new Exception();
        $error = [
            'message' => 'testing',
            'type' => E_ERROR,
            'file' => __FILE__,
            'line' => __LINE__,
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertTrue(is_string($blueScreen->render($exception, $error)));
    }
}
