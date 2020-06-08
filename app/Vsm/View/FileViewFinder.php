<?php

namespace App\Vsm\View;

use App\Vsm\VsmManager;
use Illuminate\View\FileViewFinder as BaseFileViewFinder;

class FileViewFinder extends BaseFileViewFinder
{
    /**
     * Create a new file view loader instance.
     *
     * FileViewFinder constructor.
     * @param string $viewName
     */
    public function __construct()
    {
    }

    /**
     * Find view.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function find($name)
    {
        if(isset($this->views[$name])) {
            return $this->views[$name];
        } elseif(($res = VsmManager::getLoadModuleByСode($name))) {
            return $this->views[$name] = $res;
        }

        throw new \InvalidArgumentException("View [{$name}] not found.");
    }
}
