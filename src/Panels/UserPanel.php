<?php

namespace Recca0120\LaravelTracy\Panels;

class UserPanel extends AbstractPanel
{
    public function getAttributes()
    {
        $auth = auth();

        if ($auth->check() === false) {
            $isLoggedIn = false;
            $name = 'Guest';
            $user = [

            ];
        } else {
            $isLoggedIn = true;
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
            $user = $user->toArray();
        }

        return [
            'isLoggedIn' => $isLoggedIn,
            'name' => $name,
            'user' => $user,
        ];
    }
}
