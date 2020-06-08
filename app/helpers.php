<?php

use App\Components\GeoIP\GeoIpManager;
use \App\Vsm\Exceptions\ModuleNotFound;
use \App\Vsm\Exceptions\TemplateNotFound;
use Illuminate\Support\Facades\Mail;
use Barryvdh\Debugbar\Facade as Debugbar;
use \App\Vsm\VsmManager;
use App\Vsm\View\Compiller\BladeCompiler;
use App\Services\Ots\Messages\MessageService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use \App\Models\Language;

static $language = null;
static $project = null;
static $site = [];
static $user = null;

if (!function_exists('geoip')) {
    function geoip($ip) {
        return app(GeoIpManager::class)->service()->location($ip);
    }
}

if (!function_exists('user')) {
    /**
     * @return User
     */
    function user() {
        static $user;
        /**
         * @TODO remove Graphi-Key, only for test
         */
        if(request()->header('Graphi-Key') === 'sdfUhSH7342e' && !$user) {
            $user = User::where('id', 2)->first();
        } else {
            $user = Auth::guard()->user() ?? $user ?? ($user = User::where('id', User::USER_VISITOR)->first());
        }

        return $user;
    }
}

if (!function_exists('project')) {
    /**
     * @param null $field
     * @return \App\Models\OtsProject|mixed
     */
    function project($field = null) {
        static $project;

        if(is_null($project)) {
            $project = (new \App\Services\Ots\ProjectService())->getProjectByIdOrDefault(user(), request()->project_id);
        }

        return $field ? (isset($project->{$field}) ? $project->{$field} : '') : $project;
    }
}

if (!function_exists('site')) {
    /**
     * @TODO after debug revert logic for detecting user site
     * @param null $field
     * @param null $domain
     * @return string
     */
    function site($field = null, $domain = null) {
        static $site;
        $domain = $domain ? $domain : 'onlinetutorservice.com.loc';//parse_url(request()->headers->get('referer'), PHP_URL_HOST);

        if(empty($site[$domain])) {
            $site[$domain] = (new \App\Models\Site())->getSiteByDomain($domain);
        }

        return $field ? (isset($site[$domain]->{$field}) ? $site[$domain]->{$field} : '') : $site[$domain];
    }
}

if (!function_exists('language')) {
    function language($field = null) {
        static $language;

        if(is_null($language)) {
            $language_id = Language::RUSSIAN;
            $language = (new Language())->getById($language_id);
        }

        return $field ? (isset($language->{$field}) ? $language->{$field} : '') : $language;
    }
}

if (! function_exists('vsmview')) {
    /**
     * @param null $view
     * @param array $data
     * @param array $mergeData
     * @return \App\Vsm\View\Factory|string
     * @throws Exception
     */
    function vsmview($view = null, $data = [], $mergeData = [])
    {
        try {
            /**
             * @var \App\Vsm\View\Factory $factory
             */
            $factory = app('vsmview');
            if (func_num_args() === 0) {
                return $factory;
            }
            return $factory->make($view, $data, $mergeData)->render();
        } catch (\Exception $e) {
            throw new TemplateNotFound('Template not found: ' . $view);
        }
    }
}

if (! function_exists('vsmrun')) {
    /**
     * @param $view
     * @param array $data
     * @param array $mergeData
     * @return \App\Vsm\View\Factory|string
     * @throws Exception
     */
    function vsmrun($view, $data = [], $mergeData = [])
    {
        try {
            /**
             * @var \App\Vsm\View\Factory $factory
             */
            $factory = app('vsmview');
            if (func_num_args() === 0) {
                return $factory;
            }
            return $factory->make($view, $data, $mergeData)->render();
        } catch (\Exception $e) {
            $message = $e->getPrevious() ? $e->getPrevious()->getMessage() :
                'Module not found: ' . $view;
            throw new ModuleNotFound($message);
        }
    }
}

if (! function_exists('vsmset')) {
    /**
     * @param $key_or_value
     * @param null $value
     */
    function vsmset($key_or_value, $value = null)
    {
        \App\Vsm\VsmManager::setResult($key_or_value, $value);
    }
}

if (! function_exists('vsmquery')) {
    /**
     * @param $query
     * @param string $path
     * @return mixed
     */
    function vsmquery($query, $path = '')
    {
        $result = \App\Vsm\VsmManager::query(['query' => $query]);
        if($path && $result) {
            $get = function($data, $path) use (&$get) {
                $result = null;
                if(!is_array($path)) {
                    $path = explode("|", $path);
                }
                $key = array_shift($path);
                if($path && !empty($data[$key])) {
                    $result = $get($data[$key], $path);
                } elseif(!empty($data[$key])) {
                    return $data[$key];
                }

                return $result;
            };
            return $get($result, $path);
        }

        return $result;
    }
}

if (! function_exists('vsmget')) {

    function vsmget($key = null)
    {
        return \App\Vsm\VsmManager::getResult($key);
    }
}

if (! function_exists('vsmInit')) {

    function vsmInit($modules, $site_id = null)
    {
        $site_id = $site_id ?? site('id');
        $vsmManager = new VsmManager;
        $vsmManager::initModulesByCodes($modules, $site_id);
    }
}

if (! function_exists('vsmmodule')) {
    /**
     * @param $code
     * @return \App\Vsm\Models\View|null
     */
    function vsmmodule($code)
    {
        return \App\Vsm\VsmManager::getLoadModuleByСode($code);
    }
}

if (! function_exists('vsmEmailTemplate')) {
    /**
     * @param $to_email
     * @param $templateCode
     * @param array $data
     * @param string $varCode
     */
    function vsmEmailTemplate($to_email, $templateCode, $data = [], $varCode = '', $language = Language::RUSSIAN)
    {
        Mail::to($to_email)
            ->send(
                new \App\Email\Template($to_email, $templateCode, $data, $varCode, $language)
            );

    }
}

if (! function_exists('vsmMessageTemplate')) {
    /**
     * @param $subject
     * @param $message
     * @param $from
     * @param $event_id
     * @param array $tags
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    function vsmMessage($subject, $message, $from, $event_id, $tags = []) {
        return (new MessageService(user()))->
        messageToEvent($subject, $message, $from, $event_id, $tags);
    }
}

if (! function_exists('vsmTemplate')) {
    /**
     * @param $templateCode
     * @param array $data
     * @return mixed
     */
    function vsmTemplate($templateCode, $data = []) {
        return app(BladeCompiler::class)->compileContent(
            vsmrun($templateCode, $data),
            $data
        );
    }
}

if (! function_exists('getDebugData')) {
    /**
     * @return mixed
     */
    function getDebugData() {
        return Debugbar::getFacadeRoot()->getData();
    }
}

if (! function_exists('isDebugMode')) {
    /**
     * @return mixed
     */
    function isDebugMode() {
        return config('app.debug');
    }
}


