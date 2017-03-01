<?php

namespace Recca0120\LaravelTracy;

use Tracy\Bar;
use Tracy\Debugger;
use Tracy\IBarPanel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;

class BarManager
{
    /**
     * $panels.
     *
     * @var array
     */
    protected $panels = [];

    /**
     * $bar.
     *
     * @var \Tracy\Bar
     */
    protected $bar;

    /**
     * $request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * $app.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * __construct.
     *
     * @param \Tracy\Bar                                   $bar
     * @param \Illuminate\Http\Request                     $request
     * @param \Illuminate\Contracts\Foundation\Application $app
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
     * @return \Tracy\Bar
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * loadPanels.
     *
     * @param array $panels
     *
     * @return static
     */
    public function loadPanels($panels = [])
    {
        if (isset($panels['user']) === true) {
            $panels['auth'] = $panels['user'];
            unset($panels['user']);
        }

        $ajax = $this->request->ajax();

        foreach ($panels as $name => $enabled) {
            if ($enabled === false) {
                continue;
            }

            $panel = static::make($name);
            if ($ajax === true && $panel->supportAjax === false) {
                continue;
            }
            $this->set($panel, $name);
        }

        return $this;
    }

    /**
     * make.
     *
     * @param string $id
     *
     * @return \Tracy\IBarPanel
     */
    public static function make($id)
    {
        $className = '\\'.__NAMESPACE__.'\Panels\\'.Str::studly($id).'Panel';
        $panel = new $className(new Template());

        return $panel;
    }

    /**
     * set.
     *
     * @param \Tracy\IBarPanel $panel
     * @param string           $id
     *
     * @return static
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
     * @param string $id [description]
     *
     * @return \Tracy\IBarPanel
     */
    public function get($id)
    {
        return Arr::get($this->panels, $id);
    }
}
