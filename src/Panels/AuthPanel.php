<?php

namespace Recca0120\LaravelTracy\Panels;

class AuthPanel extends AbstractPanel
{
    protected $name = 'Guest';

    protected $user = null;

    protected function loadUser()
    {
        if ($this->isLaravel() === false) {
            return;
        }

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
            $this->name = $name;
            $this->user = $userObject->toArray();
        }
    }

    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $this->loadUser();

        return [
            'name'   => $this->name,
            'user'   => $this->user,
        ];
    }
}
