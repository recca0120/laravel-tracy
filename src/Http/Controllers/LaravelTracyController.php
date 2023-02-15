<?php

namespace Recca0120\LaravelTracy\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Recca0120\LaravelTracy\DebuggerManager;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaravelTracyController extends Controller
{
    /**
     * bar.
     *
     * @param  DebuggerManager  $debuggerManager
     * @param  Request  $request
     * @param  ResponseFactory  $responseFactory
     * @return StreamedResponse
     */
    public function bar(DebuggerManager $debuggerManager, Request $request, ResponseFactory $responseFactory)
    {
        return $responseFactory->stream(function () use ($debuggerManager, $request) {
            [$headers, $content] = $debuggerManager->dispatchAssets($request->get('_tracy_bar'));
            if (headers_sent() === false) {
                foreach ($headers as $name => $value) {
                    header(sprintf('%s: %s', $name, $value), true, 200);
                }
            }
            echo $content;
        }, 200);
    }
}
