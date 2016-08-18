<?php

namespace Recca0120\LaravelTracy\Panels;

// Illuminate\Auth\Events\Login;
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
     * subscribe.
     *
     * @method subscribe
     */
    public function subscribe()
    {
        $eventName = $this->getEventName();
        $this->laravel['events']->listen($eventName, function ($event) {
            $this->loadUser();
        });
    }

    /**
     * getEventName.
     *
     * @method getEventName
     *
     * @return string
     */
    public function getEventName()
    {
        return (version_compare($this->laravel->version(), 5.2, '>=') === true) ?
            'Illuminate\Auth\Events\Login' : 'auth.login';
    }

    /**
     * loadUser.
     *
     * @method loadUser
     */
    protected function loadUser()
    {
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
        return [
            'name'   => $this->name,
            'user'   => $this->user,
        ];
    }
}
