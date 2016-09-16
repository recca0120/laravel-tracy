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

        $blueScreen->render(new Exception);
    }

    public function test_fix_stack()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $blueScreen = m::mock(new BlueScreen());

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $blueScreen->shouldAllowMockingProtectedMethods();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $blueScreen->fixStack(new Exception, [
            'message' => 'testing',
            'type' => E_ERROR,
            'file' => __FILE__,
            'line' => __LINE__,
        ]);
    }
}
