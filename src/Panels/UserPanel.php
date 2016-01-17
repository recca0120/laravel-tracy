<?php

namespace Recca0120\LaravelTracy\Panels;

class UserPanel extends AbstractPanel
{
    /**
     * initialize.
     *
     * @return void
     */
    public function boot()
    {
        $this->attributes = [
            'logged' => false,
            'name'   => 'Guest',
            'user'   => [],
        ];

        if ($this->isLaravel() === true) {
            $auth = $this->app['auth'];
            if ($auth->check() === false) {
                return;
            }
            $user = $auth->user();
            $name = $user->getAuthIdentifier();
            if (is_numeric($name)) {
                if ($user->username) {
                    $name = $user->username;
                } elseif ($user->email) {
                    $name = $user->email;
                } elseif ($user->name) {
                    $name = $user->name;
                }
            }
            $this->attributes = [
                'logged' => true,
                'name'   => $name,
                'user'   => $user->toArray(),
            ];
        }
    }
}
