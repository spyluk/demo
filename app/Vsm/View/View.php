<?php
/**
 * User: Sergei
 * Date: 19.06.19
 */

namespace App\Vsm\View;

use App\Vsm\View\Compiller\BladeCompiler;
use App\Vsm\View\Engines\CompilerEngine;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use \App\Vsm\Models\View as ViewModel;

class View extends \Illuminate\View\View implements ArrayAccess, Renderable
{
    /**
     * @var ViewModel|null
     */
    protected $model = null;

    /**
     * Create a new dbview instance.
     *
     * @param  Factory  $factory
     * @param  string  $view
     * @param  ViewModel  $model
     * @param  mixed  $data
     */
    public function __construct(Factory $factory, $view, $model, $data = [])
    {
        $this->view = $view;
        $this->path = $view;
        $this->model = $model;
        $this->engine = new CompilerEngine(app(BladeCompiler::class));
        $this->factory = $factory;
        $this->data = $data instanceof Arrayable ? $data->toArray() : (array) $data;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @return string
     */
    protected function getContents()
    {
        return $this->engine->get($this->model, $this->gatherData());
    }
}