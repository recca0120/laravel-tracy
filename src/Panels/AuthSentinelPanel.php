<?php

namespace Recca0120\LaravelTracy\Panels;

class AuthSentinelPanel extends AbstractPanel
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
        $auth = $this->laravel['sentinel'];

        $userObject = $auth->check();
        if (empty($userObject) === false) {
            if (isset($userObject->account) === true) {
                $name = $userObject->account;
            } elseif (isset($userObject->email) === true) {
                $name = $userObject->email;
            } elseif (isset($userObject->email) === true) {
                $name = $userObject->email;
            } elseif (isset($userObject->name) === true) {
                $name = $userObject->name;
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
    public function getAttributes()
    {
        $this->loadUser();

        return [
            'name' => $this->name,
            'user' => $this->user,
        ];
    }
}
