<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Recca0120\LaravelTracy\Contracts\IAjaxPanel;
use Tracy\Bar;
use Tracy\Debugger;
use Tracy\IBarPanel;

class BarManager
{
    /**
     * $panels.
     *
     * @var array
     */
    private $panels = [];

    /**
     * $bar.
     *
     * @var Bar
     */
    private $bar;

    /**
     * $request.
     *
     * @var Request
     */
    private $request;

    /**
     * @var Application
     */
    private $app;

    /**
     * __construct.
     *
     * @param  Bar  $bar
     * @param  Request  $request
     * @param  Application  $app
     */
    public function __construct(Bar $bar = null, Request $request = null, Application $app = null)
    {
        $this->bar = $bar ?: Debugger::getBar();
        $this->request = $request ?: Request::capture();
        $this->app = $app;
    }

    /**
     * getBar.
     *
     * @return Bar
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * loadPanels.
     *
     * @param  array  $panels
     * @return $this
     */
    public function loadPanels($panels = [])
    {
        if (isset($panels['user']) === true) {
            $panels['auth'] = $panels['user'];
            unset($panels['user']);
        }

        $ajax = $this->request->ajax();

        foreach ($panels as $id => $enabled) {
            if ($enabled === false) {
                continue;
            }

            if ($ajax === true && $this->isAjaxPanel($id) === false) {
                continue;
            }
            $panel = static::make($id);
            $this->set($panel, $id);
        }

        return $this;
    }

    /**
     * make.
     *
     * @param  string  $id
     * @return IBarPanel
     */
    private static function make($id)
    {
        $className = static::name($id);

        return new $className(new Template());
    }

    /**
     * set.
     *
     * @param  IBarPanel  $panel
     * @param  string  $id
     * @return $this
     */
    public function set(IBarPanel $panel, $id)
    {
        $panel->setLaravel($this->app);
        $this->panels[$id] = $panel;
        $this->bar->addPanel($panel, $id);

        return $this;
    }

    /**
     * get.
     *
     * @param  string  $id
     * @return IBarPanel
     */
    public function get($id)
    {
        return Arr::get($this->panels, $id);
    }

    /**
     * isAjaxPanel.
     *
     * @param  string  $id
     * @return bool
     */
    private function isAjaxPanel($id)
    {
        return is_subclass_of(static::name($id), IAjaxPanel::class) === true;
    }

    /**
     * name.
     *
     * @param  string  $id
     * @return string
     */
    private static function name($id)
    {
        return '\\'.__NAMESPACE__.'\Panels\\'.Str::studly($id).'Panel';
    }
}
