<?php

namespace Recca0120\LaravelTracy\Panels;

use Closure;
use Illuminate\Support\Arr;

class AuthPanel extends AbstractPanel
{
    /**
     * The user resolver callable.
     *
     * @var callable
     *
     * @return static
     */
    protected $userResolver = null;

    /**
     * setUserResolver.
     *
     * @param \Closure $userResolver
     */
    public function setUserResolver(Closure $userResolver)
    {
        $this->userResolver = $userResolver;

        return $this;
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
        $user = [
            'id' => 'Guest',
            'rows' => [],
        ];

        if ($this->isLaravel() === true) {
            $user = isset($this->laravel['sentinel']) === true ?
                $this->fromSentinel($user) :
                $this->fromGuard($user);
        } else if (is_null($this->userResolver) === false) {
            $user = array_merge($user, $this->userResolver());
        }

        var_dump($user);

        return $this->identifier($user);
    }

    protected function fromGuard($userData)
    {
        $session = $this->laravel['session'];
        $auth = $this->laravel['auth'];
        $user = $session->has($auth->getName()) === true ? $auth->user() : null;

        if (is_null($user) === false) {
            $userData = [
                'id' => $user->getAuthIdentifier(),
                'rows' => $user->toArray(),
            ];
        }

        return $userData;
    }

    protected function fromSentinel($userData)
    {
        $user = $this->laravel['sentinel']->check();

        if (empty($user) === false) {
            $userData['rows'] = $user->toArray();
            $userData['id'] = null;
        }

        return $userData;
    }

    protected function identifier($userData = [])
    {
        $id = Arr::get($userData, 'id');
        $rows = Arr::get($userData, 'rows', []);

        if (is_numeric($id) === true || empty($id) === true) {
            foreach (['username', 'account', 'email', 'name', 'id'] as $key) {
                if (isset($rows[$key]) === true) {
                    $id = $rows[$key];
                    break;
                }
            }
        }

        return [
            'id' => $id,
            'rows' => $rows,
        ];
    }
}
