<?php

namespace Recca0120\LaravelTracy\Panels;

use Closure;
use Illuminate\Support\Arr;
use Recca0120\LaravelTracy\Contracts\IAjaxPanel;

class AuthPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * The user resolver callable.
     *
     * @var callable|null
     */
    protected $userResolver = null;

    /**
     * setUserResolver.
     *
     * @param \Closure $userResolver
     * @return $this
     */
    public function setUserResolver(Closure $userResolver)
    {
        $this->userResolver = $userResolver;

        return $this;
    }

    /**
     * getAttributes.
     *
     ** @return array
     */
    protected function getAttributes()
    {
        $userData = [];

        if (is_null($this->userResolver) === false) {
            $userData['rows'] = call_user_func($this->userResolver);
        } elseif ($this->hasLaravel() === true) {
            $userData = isset($this->laravel['sentinel']) === true ?
                $this->fromSentinel() : $this->fromGuard();
        }

        return $this->identifier($userData);
    }

    /**
     * fromGuard.
     *
     * @return array
     */
    protected function fromGuard()
    {
        $userData = [];
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

    /**
     * fromSentinel.
     *
     * @return array
     */
    protected function fromSentinel()
    {
        $userData = [];
        $user = $this->laravel['sentinel']->check();

        if (empty($user) === false) {
            $userData['rows'] = $user->toArray();
            $userData['id'] = null;
        }

        return $userData;
    }

    /**
     * identifier.
     *
     * @param array $userData
     * @return array
     */
    protected function identifier($userData = [])
    {
        $id = Arr::get($userData, 'id');
        $rows = Arr::get($userData, 'rows', []);

        if (empty($rows) === true) {
            $id = 'Guest';
            $rows = [];
        } elseif (is_numeric($id) === true || empty($id) === true) {
            $id = 'UnKnown';
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
