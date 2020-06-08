<?php
/**
 * User: Sergei
 * Date: 19.06.19
 */

namespace App\Vsm\View\Engines;

use App\Vsm\VsmManager;
use Illuminate\View\Engines\CompilerEngine as BaseCompilerEngine;
use App\Vsm\View\Compiller\BladeCompiler;

class CompilerEngine extends BaseCompilerEngine
{
    /**
     * @var array
     */
    protected $result = null;

    /**
     * Create a new VsView engine instance.
     *
     * @param  BladeCompiler $compiler
     */
    public function __construct(BladeCompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Get the exception message for an exception.
     *
     * @param  \Exception  $e
     * @return string
     */
    protected function getMessage(\Exception $e)
    {
        return $e->getMessage().' (View: '.realpath(last($this->lastCompiled)->code).')';
    }

    /**
     * @param mixed $data
     */
    public function setResult($data)
    {
        $this->result = $this->result;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $code
     * @param null $site_id
     * @return mixed|null
     */
    public static function getModule($code, $site_id = null)
    {
        return VsmManager::getLoadModuleByСode($code, $site_id);
    }
}