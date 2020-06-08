<?php
/**
 * User: Sergei
 * Date: 19.06.19
 */

namespace App\Vsm\View;

use App\Vsm\View\Compiller\BladeCompiler;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('test', function($expression) {
            return "<?php echo with{$expression}->format('m/d/Y H:i'); ?>";
        });
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerViewFinder();
        $this->registerFactory();
        $this->app->bind(BladeCompiler::class, function ($app) {
            $cachePath = $app['config']['view.compiled'];
            return new BladeCompiler($app['files'], $cachePath);
        });
    }
    /**
     * Register the view environment.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->app->singleton('vsmview', function ($app) {
            $finder = $app['vsmview.finder'];
            $factory = $this->createFactory($finder, $app['events']);
            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $factory->setContainer($app);
            $factory->share('app', $app);
            return $factory;
        });
    }
    /**
     * Create a new Factory Instance.
     *
     * @param  FileViewFinder  $finder
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return Factory
     */
    protected function createFactory($finder, $events)
    {
        return new Factory($finder, $events);
    }
    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->app->bind('vsmview.finder', function ($app) {
            return new FileViewFinder();
        });
    }
}