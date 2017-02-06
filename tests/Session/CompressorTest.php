<?php

namespace Recca0120\LaravelTracy\Tests\Session;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Session\Compressor;

class CompressorTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCompress()
    {
        $compressor = new Compressor();
        $compress = $compressor->compress($input = 'foo');
        $this->assertSame($input, $compressor->decompress($compress));
    }

    public function testCompressInputIsEmpty()
    {
        $compressor = new Compressor();
        $compress = $compressor->compress($input = '');
        $this->assertSame($input, $compressor->decompress($compress));
    }
}
