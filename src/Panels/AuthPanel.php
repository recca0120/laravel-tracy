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
            $data = $this->loadUser();
            $this->name = $data['name'];
            $this->user = $data['user'];
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
        $name = 'Guest';
        $user = null;
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

        return [
            'name' => $name,
            'user' => $user,
        ];
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

        return $this->loadUser();
    }
}
