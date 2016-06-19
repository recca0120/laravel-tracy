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
            $logged = $this->laravel['auth']->check();
            if ($logged === true) {
                $userObject = $this->laravel['auth']->user();
                $name = $userObject->getAuthIdentifier();
                if (is_numeric($name)) {
                    if ($userObject->username) {
                        $name = $userObject->username;
                    } elseif ($userObject->email) {
                        $name = $userObject->email;
                    } elseif ($userObject->name) {
                        $name = $userObject->name;
                    }
                }
                $user = $userObject->toArray();
            }
        }

        return compact('logged', 'name', 'user');
    }
}
