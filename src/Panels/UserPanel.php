<?php

namespace Recca0120\LaravelTracy\Panels;

class UserPanel extends AbstractPanel
{
    public function getData()
    {
        $auth = auth();
        if (auth()->check() === false) {
            return [
                'auth' => $auth,
                'user' => [
                    'name' => 'Guest',
                    'user' => ['guest' => true],
                ],
            ];
        }

        $user = $auth->user();
        $identifier = $user->getAuthIdentifier();
        if (is_numeric($identifier)) {
            if ($user->name) {
                $identifier = $user->name;
            } elseif ($user->email) {
                $identifier = $user->email;
            }
        }

        return [
            'auth' => $auth,
            'user' => [
                'name' => $identifier,
                'user' => $user->toArray(),
            ],
        ];
    }
}
