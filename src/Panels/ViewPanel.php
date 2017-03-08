<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Recca0120\LaravelTracy\Contracts\IAjaxPanel;

class ViewPanel extends AbstractSubscriablePanel implements IAjaxPanel
{
    /**
     * $views.
     *
     * @var array
     */
    protected $views = [];

    /**
     * $limit.
     *
     * @var int
     */
    public $limit = 50;

    /**
     * subscribe.
     */
    protected function subscribe()
    {
        if (version_compare($this->laravel->version(), 5.4, '>=') === true) {
            $this->laravel['events']->listen('composing:*', function ($key, $payload) {
                $this->logView($payload[0]);
            });
        } else {
            $this->laravel['events']->listen('composing:*', function ($payload) {
                $this->logView($payload);
            });
        }
    }

    /**
     * logView.
     *
     * @param  \Illuminate\Contracts\View\View
     * @return string
     */
    protected function logView($view)
    {
        $name = $view->getName();
        $data = $this->limitCollection(Arr::except($view->getData(), ['__env', 'app']));
        $path = static::editorLink($view->getPath());
        preg_match('/href=\"(.+)\"/', $path, $m);
        $path = (count($m) > 1) ? '(<a href="'.$m[1].'">source</a>)' : '';
        $this->views[] = compact('name', 'data', 'path');
    }

    /**
     * limitCollection.
     *
     * @param array $data
     * @return array
     */
    protected function limitCollection($data)
    {
        $results = [];
        foreach ($data as $key => $value) {
            if (is_array($value) === true && count($value) > $this->limit) {
                $value = array_slice($value, 0, $this->limit);
            }

            if ($value instanceof Collection && $value->count() > $this->limit) {
                $value = $value->take($this->limit);
            }

            $results[$key] = $value;
        }

        return $results;
    }

    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'rows' => $this->views,
        ];
    }
}
