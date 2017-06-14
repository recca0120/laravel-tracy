<?php

namespace Recca0120\LaravelTracy\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Recca0120\LaravelTracy\DebuggerManager;
use Illuminate\Contracts\Routing\ResponseFactory;

class LaravelTracyController extends Controller
{
    /**
     * bar.
     *
     * @param \Recca0120\LaravelTracy\DebuggerManager $debuggerManager
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function bar(DebuggerManager $debuggerManager, Request $request, ResponseFactory $responseFactory)
    {
        return $responseFactory->stream(function () use ($debuggerManager, $request) {
            list($headers, $content) = $debuggerManager->dispatchAssets($request->get('_tracy_bar'));
            if (headers_sent() === false) {
                foreach ($headers as $name => $value) {
                    header(sprintf('%s: %s', $name, $value), true, 200);
                }
            }
            echo $content;
        }, 200);
    }
}
