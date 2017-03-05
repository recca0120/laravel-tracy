<?php

namespace Recca0120\LaravelTracy\Panels;

class SessionPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $rows = [];
        if ($this->isLaravel() === true) {
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
