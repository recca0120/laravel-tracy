<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\HtmlValidatorPanel;

class HtmlValidatorPanelTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRender()
    {
        $panel = new HtmlValidatorPanel();
        $panel->setHtml(
            $html = '<!DOCTYPE html><html><head><title>title</title></head><body></body></html>'
        );
        $panel->getTab();
        $panel->getPanel();
        $this->assertAttributeSame([
            'severenity' => [
                LIBXML_ERR_WARNING => 'Warning',
                LIBXML_ERR_ERROR => 'Error',
                LIBXML_ERR_FATAL => 'Fatal error',
            ],
            'counter' => 0,
            'errors' => [],
            'html' => $html,
        ], 'attributes', $panel);
    }
}
