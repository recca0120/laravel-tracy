<?php

namespace Recca0120\LaravelTracy\Panels;

class SessionPanel extends AbstractPanel
{
    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $data = [];
        if ($this->isLaravel() === true) {
            $session = $this->laravel['session'];
            $data = [
                'sessionId' => $session->getId(),
                'config' => $session->getSessionConfig(),
                'laravelSession' => $session->all(),
                'nativeSession' => $_SESSION,
            ];
        } else {
            $data = [
                'sessionId' => session_id(),
                'nativeSession' => $_SESSION,
            ];
        }

        return $data;
    }
}
