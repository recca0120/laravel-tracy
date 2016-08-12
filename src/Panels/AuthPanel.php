<?php

namespace Recca0120\LaravelTracy\Panels;

class AuthPanel extends AbstractPanel
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
        $logged = false;
        $name = 'Guest';
        $user = [];
        if ($this->isLaravel() === true) {
            $token = $this->laravel['session']->token();
            $userObject = $this->laravel['auth']->user();
            if (is_null($userObject) === false) {
                $name = $userObject->getAuthIdentifier();
                if (is_numeric($name)) {
                    if (isset($userObject->username) === true) {
                        $name = $userObject->username;
                    } elseif (isset($userObject->email) === true) {
                        $name = $userObject->email;
                    } elseif (isset($userObject->name) === true) {
                        $name = $userObject->name;
                    }
                }
                $user = $userObject->toArray();
            }
            $this->laravel['session']->put('_token', $token);
        }

        return compact('logged', 'name', 'user');
    }
}
