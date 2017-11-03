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
        parent::tearDown();
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
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $this->assertSame($config['enabled'], $debuggerManager->enabled());
    }

    public function testShowBar()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['showBar' => true],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $this->assertSame($config['showBar'], $debuggerManager->showBar());
    }

    public function testAccepts()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['accepts' => ['foo']],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $this->assertSame($config['accepts'], $debuggerManager->accepts());
    }

    public function testDispatchAssetsCss()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $content = 'foo';
        $bar->shouldReceive('dispatchAssets')->once()->andReturnUsing(function () use ($content) {
            echo $content;
        });

        $this->assertSame([
            [
                'Content-Type' => 'text/css; charset=utf-8',
                'Cache-Control' => 'max-age=86400',
                'Content-Length' => strlen($content),
            ],
            $content,
        ], $debuggerManager->dispatchAssets('css'));
    }

    public function testDispatchAssetsJs()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['accepts' => ['foo']],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $content = 'foo';
        $bar->shouldReceive('dispatchAssets')->once()->andReturnUsing(function () use ($content) {
            echo $content;
        });

        $this->assertSame([
            [
                'Content-Type' => 'text/javascript; charset=utf-8',
                'Cache-Control' => 'max-age=86400',
                'Content-Length' => strlen($content),
            ],
            $content,
        ], $debuggerManager->dispatchAssets('js'));
    }

    public function testDispatchAssetsContentId()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $session->shouldReceive('isStarted')->once()->andReturn(false);
        $session->shouldReceive('start')->once();

        $content = 'foo';
        $bar->shouldReceive('dispatchAssets')->once()->andReturnUsing(function () use ($content) {
            echo $content;
        });

        $this->assertSame([
            [
                'Content-Type' => 'text/javascript; charset=utf-8',
                'Content-Length' => strlen($content),
            ],
            $content,
        ], $debuggerManager->dispatchAssets(uniqid()));
    }

    public function testShutdownHandlerAndSessionIsClose()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $session->shouldReceive('isStarted')->once()->andReturn(false);

        $barRender = 'foo';
        $bar->shouldReceive('render')->once()->andReturnUsing(function () use ($barRender) {
            echo $barRender;
        });

        $content = '<html><head></head><body></body></html>';

        $this->assertSame('<html><head></head><body>'.$barRender.'</body></html>', $debuggerManager->shutdownHandler($content));
    }

    public function testShutdownHandler()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $session->shouldReceive('isStarted')->once()->andReturn(true);

        $loader = '<script async></script>';
        $bar->shouldReceive('renderLoader')->once()->andReturnUsing(function () use ($loader) {
            echo $loader;
        });

        $barRender = 'foo';
        $bar->shouldReceive('render')->once()->andReturnUsing(function () use ($barRender) {
            echo $barRender;
        });

        $content = '<html><head></head><body></body></html>';

        $this->assertSame('<html><head>'.$loader.'</head><body>'.$barRender.'</body></html>', $debuggerManager->shutdownHandler($content));
    }

    public function testShutdownHandlerAppendToHtml()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['appendTo' => 'html'],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $session->shouldReceive('isStarted')->once()->andReturn(true);

        $loader = '<script async></script>';
        $bar->shouldReceive('renderLoader')->once()->andReturnUsing(function () use ($loader) {
            echo $loader;
        });

        $barRender = 'foo';
        $bar->shouldReceive('render')->once()->andReturnUsing(function () use ($barRender) {
            echo $barRender;
        });

        $content = '<html><head></head><body></body></html>';

        $this->assertSame('<html><head>'.$loader.'</head><body></body>'.$barRender.'</html>', $debuggerManager->shutdownHandler($content));
    }

    public function testShutdownHandlerHasError()
    {
        $debuggerManager = new DebuggerManager(
            $config = [],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );
        $content = '';
        $error = [
            'message' => 'testing',
            'type' => E_ERROR,
            'file' => __FILE__,
            'line' => __LINE__,
        ];

        $blueScreen->shouldReceive('render')->once()->with(m::on(function ($errorException) use ($error) {
            $this->assertSame($error['type'], $errorException->getSeverity());
            $this->assertSame($error['message'], $errorException->getMessage());
            $this->assertSame(0, $errorException->getCode());
            $this->assertSame($error['file'], $errorException->getFile());
            $this->assertSame($error['line'], $errorException->getLine());

            return true;
        }))->andReturnUsing(function () use ($content) {
            echo $content;
        });

        $this->assertSame($content, $debuggerManager->shutdownHandler($content, $error));
    }

    public function testExceptionHandler()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['accepts' => ['foo']],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $exception = new Exception();
        $content = 'foo';
        $blueScreen->shouldReceive('render')->once()->with($exception)->andReturnUsing(function () use ($content) {
            echo $content;
        });
        $this->assertSame($content, $debuggerManager->exceptionHandler($exception));
    }

    public function testReplacePath()
    {
        $debuggerManager = new DebuggerManager(
            $config = ['accepts' => ['foo']],
            $bar = m::mock('Tracy\Bar'),
            $blueScreen = m::mock('Tracy\BlueScreen'),
            $session = m::mock('Recca0120\LaravelTracy\Session')
        );

        $session->shouldReceive('isStarted')->once()->andReturn(true);

        $debuggerManager->setUrlGenerator(
            $urlGenerator = m::mock('Illuminate\Contracts\Routing\UrlGenerator')
        );

        $urlGenerator->shouldReceive('route')->twice()->andReturn($root = 'foo');

        $bar->shouldReceive('renderLoader')->once()->andReturnUsing(function () {
            echo '<script src="?_tracy_bar=foo" async></script>';
        });

        $bar->shouldReceive('render')->once()->andReturnUsing(function () {
            echo '?_tracy_bar=foo';
        });

        $this->assertSame('<head><script src="foo?_tracy_bar=foo" async></script></head><body>foo?_tracy_bar=foo</body>', $debuggerManager->shutdownHandler('<head></head><body></body>'));
    }
}
