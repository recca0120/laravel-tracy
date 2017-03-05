<?php

namespace Recca0120\LaravelTracy\Events;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BeforeBarRender
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
