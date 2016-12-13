<?php

use Mockery as m;
use Recca0120\LaravelTracy\Session\Compressor;

class CompressorTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_compress_decompress()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $input = 'foo';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $compressor = new Compressor();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($input, $compressor->decompress($compressor->compress($input)));
    }

    public function test_empty_data()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $input = '';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $compressor = new Compressor();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($input, $compressor->decompress($compressor->compress($input)));
    }
}
