<?php

namespace Recca0120\LaravelTracy\Panels;

class AuthPanel extends AbstractPanel
{
    /**
     * $name.
     *
     * @var string
     */
    protected $name = 'Guest';

    /**
     * $user.
     *
     * @var array
     */
    protected $user = null;

    /**
     * loadUser.
     *
     * @method loadUser
     */
    protected function loadUser()
    {
        $name = 'Guest';
        $user = null;
        $session = $this->laravel['session'];
        $auth = $this->laravel['auth'];
        if ($session->has($auth->getName()) === false) {
            return;
        }
        $userObject = $auth->user();
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

        $this->name = $name;
        $this->user = $user;
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
            'name' => $this->name,
            'user' => $this->user,
        ];
    }
}
