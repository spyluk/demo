<?php
/**
 * User: Sergei
 * Date: 31.05.19
 */

namespace App\GraphQL\Traits;

use App\Components\Api\Response;
use App\GraphQL\Error\ValidationError;
use App\GraphQL\Support\SelectFields;
use App\Models\User;
use App\Models\Site as SiteModel;
use App\Models\Language as LanguageModel;
use App\Services\Ots\ProjectService;
use App\Validators\DefaultValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Closure;
use Rebing\GraphQL\Error\AuthorizationError;
use Illuminate\Support\Facades\Validator;


trait BaseTrait
{
    /**
     * @var null
     */
    protected static $language = null;
    /**
     * @var null
     */
    protected static $sites = [];
    /**
     * @var null
     */
    protected static $project = null;

    /**
     * @return mixed
     */
    public function ip()
    {
        return request()->ip();
    }

    /**
     * get authorized user or user visitor
     *
     * @return null|User
     */
    public static function user()
    {
        return user();
    }

    /**
     * @param null $field
     * @return array|string
     */
    public function language($field = null)
    {
        if(is_null(self::$language)) {
            $language_id = $this->user() ? $this->user()->getLanguageId() : LanguageModel::RUSSIAN;
            self::$language = (new LanguageModel)->getSiteLanguageByLanguageId($this->site('id'), $language_id);
        }

        return $field ? (isset(self::$language->{$field}) ? self::$language->{$field} : '') : self::$language;
    }

    public function site($field = null)
    {
        return site($field);
    }

    /**
     * Override this in your queries or mutations
     * to provide custom authorization
     */
    public function authorize(array $args): bool
    {
        return !!self::user();
    }

    /**
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return true;//self::user()->hasPermissionTo();
    }

    /**
     * @param $error_code
     * @param $messages
     * @return array
     */
    public function error($error_code, $messages = '')
    {
        $return = ['status' => 'error', 'error' => $error_code];

        if($messages) {
            $return['messages'] = $messages;
        }

        return $return;
    }

    /**
     * @return array
     */
    public function authError()
    {
        return $this->error(Response::AUTH_ERROR);
    }

    /**
     * @param $array
     * @param string $separate
     * @return string
     */
    protected function getKeyValueSeparateString($array, $separate = ', ')
    {
        return implode($separate, array_map(function($item, $key){
            return $key . ' - ' . $item;
        }, $array, array_keys($array)));
    }

    protected function getResolver(): ?Closure
    {
        if (! method_exists($this, 'resolve')) {
            return null;
        }

        $resolver = [$this, 'resolve'];
        $authorize = [$this, 'authorize'];

        return function () use ($resolver, $authorize) {
            $arguments = func_get_args();

            // Get all given arguments
            if (! is_null($arguments[2]) && is_array($arguments[2])) {
                $arguments[1] = array_merge($arguments[1], $arguments[2]);
            }

            // Validate mutation arguments
            if (method_exists($this, 'getRules')) {
                $args = Arr::get($arguments, 1, []);
                $rules = call_user_func_array([$this, 'getRules'], [$args]);
                if (count($rules)) {

                    // allow our error messages to be customised
                    $messages = $this->validationErrorMessages($args);

//                    $validator = Validator::make($args, $rules, $messages);
                    $validator = new DefaultValidator($args, $rules, $messages);
                    if ($validator->fails()) {
                        throw new ValidationError('validation', $validator);
                    }
                }
            }

            // Authorize
            if (call_user_func($authorize, $arguments[1]) != true) {
                throw new AuthorizationError('Unauthorized');
            }

            // Add the 'selects and relations' feature as 5th arg
            if (isset($arguments[3])) {
                $arguments[] = function (int $depth = null) use ($arguments): SelectFields {
                    $ctx = $arguments[2] ?? null;

                    return new SelectFields($arguments[3], $this->type(), $arguments[1], $depth ?? 5, $ctx);
                };
            }

            return call_user_func_array($resolver, $arguments);
        };
    }

}