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
    protected function getAttributes()
    {
        $data = [];
        if ($this->isLaravel() === true) {
            $session = $this->laravel['session'];
            $data = [
                'sessionId'      => $session->getId(),
                'config'         => $session->getSessionConfig(),
                'laravelSession' => $session->all(),
            ];

            if (session_status() == PHP_SESSION_ACTIVE) {
                $data['nativeSession'] = $_SESSION;
            }
        } else {
            // PHP < 5.4.0
            // if(session_id() == '') {
            //     session_start();
            // }
            if (session_status() == PHP_SESSION_ACTIVE) {
                $data = [
                    'sessionId'      => session_id(),
                    'nativeSession'  => $_SESSION,
                ];
            }
        }

        return $data;
    }
}
