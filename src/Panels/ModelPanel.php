<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Support\Str;
use Recca0120\LaravelTracy\Contracts\IAjaxPanel;

class ModelPanel extends AbstractSubscriablePanel implements IAjaxPanel
{
    /**
     * $models.
     *
     * @var array
     */
    protected $models = [];

    /**
     * $total number of models.
     *
     * @var int
     */
    protected $total = 0;

    /**
     * subscribe.
     */
    protected function subscribe()
    {
        $events = $this->laravel['events'];
        $events->listen('eloquent.*', function ($event, $models) {
            if (Str::contains($event, 'eloquent.retrieved')) {
                foreach ($models as $model) {
                    $class = get_class($model);
                    $this->models[$class] = ($this->models[$class] ?? 0) + 1;
                    $this->total++;
                }
            }
        });
    }

    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'total' => $this->total,
            'models' => $this->models,
        ];
    }
}
