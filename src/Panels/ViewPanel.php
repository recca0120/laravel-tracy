<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class ViewPanel extends AbstractPanel
{
    /**
     * $views.
     *
     * @var array
     */
    protected $views = [];

    /**
     * $limit.
     */
    public $limit = 50;

    /**
     * subscribe.
     *
     * @method subscribe
     */
    public function subscribe()
    {
        $this->laravel['events']->listen('composing:*', function ($view) {
            $name = $view->getName();
            $data = $this->limitCollection(Arr::except($view->getData(), ['__env', 'app']));

            $path = self::editorLink($view->getPath());
            preg_match('/href=\"(.+)\"/', $path, $m);
            $path = (count($m) > 1) ? '(<a href="'.$m[1].'">source</a>)' : '';
            $this->views[] = compact('name', 'data', 'path');
        });
    }

    /**
     * limitCollection.
     *
     * @method limitCollection
     *
     * @param  array $data
     *
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
     * @method getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'views' => $this->views,
        ];
    }
}
