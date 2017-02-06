<?php

namespace Recca0120\LaravelTracy\Tests;

use Exception;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\BlueScreen;

class BlueScreenTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRender()
    {
        $blueScreen = new BlueScreen();
        $this->assertTrue(is_string($blueScreen->render(
            $exception = new Exception(),
            $error = [
                'message' => 'testing',
                'type' => E_ERROR,
                'file' => __FILE__,
                'line' => __LINE__,
            ]
        )));
    }
}
