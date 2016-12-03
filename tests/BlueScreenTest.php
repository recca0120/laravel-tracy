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
        | Arrange
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
        | Act
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertTrue(is_string($blueScreen->render($exception, $error)));
    }
}
