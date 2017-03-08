<?php

namespace Recca0120\LaravelTracy\Panels;

use Recca0120\LaravelTracy\Contracts\IAjaxPanel;

class SessionPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes()
    {
        $rows = [];
        if ($this->hasLaravel() === true) {
            $session = $this->laravel['session'];
            $rows = [
                'sessionId' => $session->getId(),
                'sessionConfig' => $session->getSessionConfig(),
                'laravelSession' => $session->all(),
            ];
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $rows['nativeSessionId'] = session_id();
            $rows['nativeSession'] = $_SESSION;
        }

        return compact('rows');
    }
}
