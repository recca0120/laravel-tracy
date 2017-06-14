<?php

namespace Recca0120\LaravelTracy\Http\Controllers;

use Illuminate\Routing\Controller;
use Recca0120\LaravelTracy\DebuggerManager;
use Illuminate\Contracts\Routing\ResponseFactory;

class LaravelTracyController extends Controller
{
    /**
     * index.
     *
     * @param \Recca0120\LaravelTracy\DebuggerManager $debuggerManager
     * @param \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function index(DebuggerManager $debuggerManager, ResponseFactory $responseFactory, $type)
    {
        return $responseFactory->stream(function () use ($debuggerManager, $type) {
            list($headers, $content) = $debuggerManager->dispatchAssets($type);
            if (headers_sent() === false) {
                foreach ($headers as $name => $value) {
                    header(sprintf('%s: %s', $name, $value), true, 200);
                }
            }
            echo $content;
        }, 200);
    }
}
