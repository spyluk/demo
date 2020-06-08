<?php
/**
 *
 * User: sergei
 * Date: 13.10.18
 * Time: 13:34
 */

namespace App\Vsm\View\Compiller;

use App\Vsm\Models\View as ViewModel;
use Illuminate\View\Compilers\BladeCompiler as LaravelBladeCompiler;

class BladeCompiler extends LaravelBladeCompiler {

    /**
     * Compile the view from the given model.
     *
     * @param  ViewModel $model
     * @return void
     */
    public function compile($model = null)
    {
        if (is_null($model)) {
            return;
        }

        $contents = $this->compileString($model->content);
        if (! is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($model), $contents);
        }
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  ViewModel  $model
     * @return string
     */
    public function getCompiledPath($model)
    {
        /*
         * A unique path for the given model instance must be generated
         * so the view has a place to cache. The following generates a
         * path using almost the same logic as Blueprint::createIndexName()
         *
         * e.g db_table_name_id_4
         */
        $path = 'sv_'.$model->code;
        $path = strtolower(str_replace(['-', '.'], '_', $path));
        return $this->cachePath.'/'.md5($path);
    }

    /**
     * Determine if the view for the given model is expired.
     *
     * @param  ViewModel  $model
     * @return bool
     */
    public function isExpired($model)
    {
        if (! config('vs.cache')) {
            return true;
        }

        $compiled = $this->getCompiledPath($model);
        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (! $this->cachePath || ! $this->files->exists($compiled)) {
            return true;
        }
        $lastModified = $model->updated_at;
        return $lastModified >= $this->files->lastModified($compiled);
    }

    /**
     * Compile blade template with passing arguments.
     *
     * @param string $value HTML-code including blade
     * @param array $args Array of values used in blade
     * @return string
     * @throws \Exception
     */
    public function compileContent($content, array $args = array())
    {
        $generated = parent::compileString($content);
        ob_start() and extract($args, EXTR_SKIP);

        // We'll include the view contents for parsing within a catcher
        // so we can avoid any WSOD errors. If an exception occurs we
        // will throw it out to the exception handler.
        try
        {
            eval('?>'.$generated);
        }

            // If we caught an exception, we'll silently flush the output
            // buffer so that no partially rendered views get thrown out
            // to the client and confuse the user with junk.
        catch (\Exception $e)
        {
            ob_get_clean(); throw $e;
        }

        $content = ob_get_clean();

        return $content;
    }

}