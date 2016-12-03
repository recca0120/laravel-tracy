<?php

use Mockery as m;
use Recca0120\LaravelTracy\Panels\HtmlValidatorPanel;

class HtmlValidatorPanelTest extends PHPUnit_Framework_TestCase
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

        $html = '<!DOCTYPE html><html><head><title>title</title></head><body></body></html>';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $panel = new HtmlValidatorPanel();
        $panel->setHtml($html);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'severenity' => [
                LIBXML_ERR_WARNING => 'Warning',
                LIBXML_ERR_ERROR => 'Error',
                LIBXML_ERR_FATAL => 'Fatal error',
            ],
            'counter' => 0,
            'errors' => [],
            'html' => $html,
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));
    }
}
