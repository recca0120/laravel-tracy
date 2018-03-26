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
        $attributes = [];
        if (is_null($this->userResolver) === false) {
            $attributes['rows'] = call_user_func($this->userResolver);
        } elseif ($this->hasLaravel() === true) {
            $attributes = isset($this->laravel['sentinel']) === true ?
                $this->fromSentinel() : $this->fromGuard();
        }

        return $this->identifier($attributes);
    }

    /**
     * fromGuard.
     *
     * @return array
     */
    protected function fromGuard()
    {
        $auth = $this->laravel['auth'];
        $user = $auth->user();

        return is_null($user) === true ? [] : [
            'id' => $user->getAuthIdentifier(),
            'rows' => $user->toArray(),
        ];
    }

    /**
     * fromSentinel.
     *
     * @return array
     */
    protected function fromSentinel()
    {
        $user = $this->laravel['sentinel']->check();

        return empty($user) === true ? [] : [
            'id' => null,
            'rows' => $user->toArray(),
        ];
    }

    /**
     * identifier.
     *
     * @param array $attributes
     * @return array
     */
    protected function identifier($attributes = [])
    {
        $id = Arr::get($attributes, 'id');
        $rows = Arr::get($attributes, 'rows', []);

        if (empty($rows) === true) {
            $id = 'Guest';
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
