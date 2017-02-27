<?php

namespace Recca0120\LaravelTracy\Tests;

use Exception;
use Mockery as m;
use Tracy\Debugger;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\DebuggerManager;

class DebuggerManagerTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testInit()
    {
        DebuggerManager::init($config = [
            'showBar' => true,
            'editor' => 'foo',
            'maxDepth' => 10,
            'maxLength' => 100,
            'scream' => false,
            'showLocation' => false,
            'strictMode' => false,
            'currentTime' => 12345678,
            'editorMapping' => [],
        ]);

        $this->assertSame($config['editor'], Debugger::$editor);
        $this->assertSame($config['maxDepth'], Debugger::$maxDepth);
        $this->assertSame($config['maxLength'], Debugger::$maxLength);
        $this->assertSame($config['scream'], Debugger::$scream);
        $this->assertSame($config['showLocation'], Debugger::$showLocation);
        $this->assertSame($config['strictMode'], Debugger::$strictMode);
        $this->assertSame($config['currentTime'], Debugger::$time);

        if (isset(Debugger::$editorMapping) === true) {
            $this->assertSame($config['editorMapping'], Debugger::$editorMapping);
        }
    }

    public function testEnabled()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['enabled' => true],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $this->assertSame($config['enabled'], $debuggerManager->enabled());
    }

    public function testShowBar()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['showBar' => true],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $this->assertSame($config['showBar'], $debuggerManager->showBar());
    }

    public function testAccepts()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['accepts' => ['foo']],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $this->assertSame($config['accepts'], $debuggerManager->accepts());
    }

    public function testDispatchAssetsCss()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $content = 'foo';
        $bar->shouldReceive('dispatchAssets')->once()->andReturnUsing(function() use ($content) {
            echo $content;
        });

        $this->assertSame([
            [
                'content-type' => 'text/css; charset=utf-8',
                'cache-control' => 'max-age=86400',
                'content-length' => strlen($content)
            ],
            $content,
        ], $debuggerManager->dispatchAssets('css'));
    }

    public function testDispatchAssetsJs()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['accepts' => ['foo']],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $content = 'foo';
        $bar->shouldReceive('dispatchAssets')->once()->andReturnUsing(function() use ($content) {
            echo $content;
        });

        $this->assertSame([
            [
                'content-type' => 'text/javascript; charset=utf-8',
                'cache-control' => 'max-age=86400',
                'content-length' => strlen($content)
            ],
            $content,
        ], $debuggerManager->dispatchAssets('js'));
    }

    public function testDispatchAssetsAssets()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $content = 'foo';
        $bar->shouldReceive('dispatchAssets')->once()->andReturnUsing(function() use ($content) {
            echo $content;
        });

        $this->assertSame([
            [
                'content-type' => 'text/javascript; charset=utf-8',
                'cache-control' => 'max-age=86400',
                'content-length' => strlen($content)
            ],
            $content,
        ], $debuggerManager->dispatchAssets('assets'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testDispatchAssetsContentId()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $content = 'foo';

        $bar->shouldReceive('dispatchContent')->andReturnUsing(function() use ($content) {
            echo $content;
        });
        $bar->shouldReceive('dispatchAssets')->andReturnUsing(function() use ($content) {
            echo $content;
        });

        $this->assertSame([
            [
                'content-type' => 'text/javascript; charset=utf-8',
                'content-length' => strlen($content)
            ],
            $content,
        ], $debuggerManager->dispatchAssets(uniqid()));
    }

    public function testShutdownHandler()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $barRender = 'foo';
        $bar->shouldReceive('render')->once()->andReturnUsing(function() use ($barRender) {
            echo $barRender;
        });

        $content = '<html><body></body></html>';

        $this->assertSame('<html><body>'.$barRender.'</body></html>', $debuggerManager->shutdownHandler($content));
    }

    public function testShutdownHandlerAppendToHtml()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['appendTo' => 'html'],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $barRender = 'foo';
        $bar->shouldReceive('render')->once()->andReturnUsing(function() use ($barRender) {
            echo $barRender;
        });

        $content = '<html><body></body></html>';

        $this->assertSame('<html><body></body>'.$barRender.'</html>', $debuggerManager->shutdownHandler($content));
    }

    public function testShutdownHandlerHasError()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );
        $content = '';
        $error = [
            'message' => 'testing',
            'type' => E_ERROR,
            'file' => __FILE__,
            'line' => __LINE__,
        ];

        $blueScreen->shouldReceive('render')->once()->with(m::on(function($errorException) use ($error) {
            $this->assertSame($error['type'], $errorException->getSeverity());
            $this->assertSame($error['message'], $errorException->getMessage());
            $this->assertSame(0, $errorException->getCode());
            $this->assertSame($error['file'], $errorException->getFile());
            $this->assertSame($error['line'], $errorException->getLine());

            return true;
        }))->andReturnUsing(function() use ($content) {
            echo $content;
        });

        $this->assertSame($content, $debuggerManager->shutdownHandler($content, $error));
    }

    public function testExceptionHandler()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['accepts' => ['foo']],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen')
        );

        $exception = new Exception();
        $content = 'foo';
        $blueScreen->shouldReceive('render')->once()->with($exception)->andReturnUsing(function() use ($content) {
            echo $content;
        });
        $this->assertSame($content, $debuggerManager->exceptionHandler($exception));
    }
}
