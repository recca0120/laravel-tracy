<?php

namespace Recca0120\LaravelTracy\Panels;

class AuthPanel extends AbstractPanel
{
    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $rows = [];
        $id = 'Guest';
        if ($this->isLaravel() === true) {
            $session = $this->laravel['session'];
            $auth = $this->laravel['auth'];

            if ($session->has($auth->getName()) === true) {
                $userObject = $auth->user();
                if (is_null($userObject) === false) {
                    $rows = $userObject->toArray();
                    $id = $userObject->getAuthIdentifier();
                    if (is_numeric($id)) {
                        foreach (['username', 'email', 'name'] as $key) {
                            if (isset($rows[$key]) === true) {
                                $id = $rows[$key];
                                break;
                            }
                        }
                    }
                }
            }
        }

        return compact('id', 'rows');
    }
}
