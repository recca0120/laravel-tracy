<?php

namespace Recca0120\LaravelTracy\Panels;

class UserPanel extends AbstractPanel
{
    protected $auth;

    protected $user;

    public function __construct()
    {
        $app = app();
        $auth = $app['auth'];
        try {
            $user = $auth->user();
        } catch (\Exception $e) {
            $user = null;
        }
        $user = $this->getUserInformation($user);
        $this->setData([
            'auth' => $auth,
            'user' => $user,
        ]);
    }
    /**
     * Get displayed user information.
     *
     * @param \Illuminate\Auth\UserInterface $user
     *
     * @return array
     */
    protected function getUserInformation($user = null)
    {
        // Defaults
        if (is_null($user)) {
            return [
                'name' => 'Guest',
                'user' => ['guest' => true],
            ];
        }

        // The default auth identifer is the ID number, which isn't all that
        // useful. Try username and email.
        $identifier = $user->getAuthIdentifier();
        if (is_numeric($identifier)) {
            try {
                if ($user->username) {
                    $identifier = $user->username;
                } elseif ($user->email) {
                    $identifier = $user->email;
                }
            } catch (\Exception $e) {
            }
        }

        return [
            'name' => $identifier,
            'user' => $user instanceof ArrayableInterface ? $user->toArray() : $user,
        ];
    }
}
