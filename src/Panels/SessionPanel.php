<?php

namespace Recca0120\LaravelTracy\Panels;

class SessionPanel extends AbstractPanel
{
    public function getAttributes()
    {
        $app = app();
        $session = $app['session'];

        return [
            'sessionId' => $session->getId(),
            'config' => $session->getSessionConfig(),
            'sessionData' => $session->all(),
        ];
    }
}
