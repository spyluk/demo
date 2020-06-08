<?php

namespace App\Vsm\View;

use App\Vsm\Models\View as ViewModel;
use Illuminate\View\Factory as BaseFactory;
use \Illuminate\Contracts\Events\Dispatcher;

class Factory extends BaseFactory
{

    /**
     * Create a new dbview factory instance.
     *
     * @param FileViewFinder  $finder
     * @param Dispatcher $events
     */
    public function __construct(FileViewFinder $finder, Dispatcher $events)
    {
        $this->finder = $finder;
        $this->events = $events;
        $this->share('__env', $this);
    }

    /**
     * Create a new view instance from the given arguments.
     *
     * @param  string  $view
     * @param  ViewModel  $model
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @return View
     */
    protected function viewInstance($view, $model, $data)
    {
        $data = array_merge($data, [
            '__view' => $model
        ]);

        return new View($this, $view, $model, $data);
    }
}
