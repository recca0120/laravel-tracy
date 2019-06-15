<?php

namespace Recca0120\LaravelTracy\Tests;

use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Editor;

class EditorTest extends TestCase
{
    public function testSublime()
    {
        $expected = 'subl://open?url=file://%file&line=%line';
        $this->assertEquals($expected, Editor::openWith('subl'));
        $this->assertEquals($expected, Editor::openWith('sublime'));
    }

    public function testPhpStorm()
    {
        $expected = 'phpstorm://open?file=%file&line=%line';
        $this->assertEquals($expected, Editor::openWith('phpstorm'));
    }

    public function testVsCode()
    {
        $expected = 'vscode://file/%file:%line';
        $this->assertEquals($expected, Editor::openWith('vscode'));
    }

    public function testMacVim()
    {
        $expected = 'mvim://open/?url=file://%file&line=%line';
        $this->assertEquals($expected, Editor::openWith('mvim'));
        $this->assertEquals($expected, Editor::openWith('macvim'));
    }

    public function testTextMate()
    {
        $expected = 'txmt://open/?url=file://%file&line=%line';
        $this->assertEquals($expected, Editor::openWith('txmt'));
        $this->assertEquals($expected, Editor::openWith('textmate'));
    }

    public function testDefault()
    {
        $expected = 'editor://open/?file=%file&line=%line';
        $this->assertEquals($expected, Editor::openWith('otherEditor'));
    }

    public function testTextCase()
    {
        $expected = 'vscode://file/%file:%line';
        $this->assertEquals($expected, Editor::openWith('VSCode'));
    }
}
