<?php

namespace Recca0120\LaravelTracy\Events;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BeforeBarRender
{
    /**
     * The request instance.
     *
     * @var Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  Request  $request
     * @param  Response  $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
