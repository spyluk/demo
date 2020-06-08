<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use App\Models\Traits\BaseTrait;
use League\Flysystem\Exception;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
//use Spatie\Permission\Traits\HasRoles;
use App\Date;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable;
    use BaseTrait {
        create as createParent;
    }
//    use HasRoles {
//        hasPermissionTo as hasPermissionToParent;
//        assignRole as assignRoleParent;
//    }
    use HasMediaTrait;

    /**
     *
     */
    const CODE_SHORT_TEXT = 'about.short_text';
    /**
     *
     */
    const CODE_TEXT = 'about.text';
    /**
     * Default user
     */
    const USER_VISITOR = 1;
    /**
     * Admin user
     */
    const USER_ADMIN = 2;

    /**
     *
     */
    const MEDIA_AVATAR = 'avatar';

    /**
     * @var string
     */
    public $guard_name = 'api';
    /**
     * @var null
     */
    protected $current_project = null;
    /**
     * @var null
     */
    protected $tagPermissionModels = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /**
     * @var array
     */
    protected $fillable = [
        'uid',
        'site_id',
        'type_id',
        'country_id',
        'timezone_id',
        'language_id',
        'user_name',
        'first_name',
        'last_name',
        'confirmation_code',
        'email',
        'password',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param $email
     * @param $password
     * @return User|null
     */
    public static function getByCredential($email, $password)
    {
        $user = static::where('email', $email)->first();
        return ($user && Hash::check($password, $user->password)) ? $user : null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function tags()
    {
        return $this->hasMany('App\Models\CustomVarRelate', 'model_id')
            ->join('custom_var_codes as cvcode', 'cvcode.var_id', '=', 'custom_var_relates.var_id')
            ->where('custom_var_relates.model_type', $this->quote(self::class));
    }

    public function properties()
    {
        return $this->hasMany('App\Models\CustomVarRelate', 'model_id')
            ->where('custom_var_relates.model_type', $this->quote(self::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function about()
    {
        $hasMany = $this->hasManyThrough(
            CustomVarValue::class,
            CustomVarRelate::class,
            'model_id', 'var_id', 'user_id', 'var_id')
            ->join('custom_vars', 'custom_vars.id', '=', 'custom_var_relates.var_id')
            ->join('category_codes', 'category_codes.category_id', '=', 'custom_vars.category_id');
        $hasMany->getQuery()
            ->where('model_type', self::class)
            ->where('custom_var_values.language_id', Language::ENGLISH)
            ->select('*', 'category_codes.code');

        return $hasMany;
    }


    /**
     * @return $this
     */
    public function tagPermissionModels()
    {
        if(!$this->tagPermissionModels) {
            $builder = (new CustomVarModel)
                ->select('custom_var_models.*')
                ->leftJoin('custom_var_codes as cvcode', 'custom_var_models.var_id', '=', 'cvcode.var_id')
                ->leftJoin('custom_var_relates as cvr', 'custom_var_models.var_id', '=', 'cvr.var_id')
                ->where('cvr.owner_model_type', $this->quote(OtsProject::class))
                ->where('cvr.owner_model_id', $this->quote($this->getCurrentProject('id')))
                ->where('cvr.model_type', $this->quote(self::class))
                ->where('cvr.model_id', $this->quote($this->getId()))
                ->where('custom_var_models.active', $this->quote(true));

            $builder = (new CustomVar)->queryByModels(
                $builder,
                [
                    Site::class => $this->getSiteId(),
                    OtsProject::class => $this->getCurrentProject('id')
                ],
                false,
                'custom_var_models'
            );

            $this->tagPermissionModels = $builder->get();
        }

        return $this->tagPermissionModels;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function site()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function avatar()
    {
        $hasOne = $this->hasOne('App\Models\Media', 'model_id');
        $hasOne->getQuery()->where('model_type', self::class);
        return $hasOne;
    }

    /**
     * @return int
     */
    public function getSiteId()
    {
        return $this->site_id;
    }

    /**
     * @return string
     */
    public function getFulleName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * @param string $permission
     * @param string|null $guardName
     * @return bool
     */
    public function hasPermissionTo(string $permission, string $guardName = null): bool
    {
        $return = false;
        /**
         * @var CustomVarModel $item
         */
        foreach($this->tagPermissionModels() as $item)
        {
            try
            {
                $return = $item->hasPermissionToParent($permission, $guardName);
                if($return) {
                    break;
                }
            }
            catch (\Exception $e)
            {
            }
        }
        return $return;
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws Exception
     */
    public function create(array $data)
    {
        $user = null;
        if($data = $this->prepareData($data)) {
            /**
             * @var User $user
             */
            $user = $this->createParent($data);
        } else {
            throw new Exception('No fields are filled, or hidden fields are filled!');
        }

        return $user;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $fields = $this->getFillable();
        foreach ($data as $key => $value) {
            if($key === 'password')
            {

            }elseif (!in_array($key, $fields)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * @param string $date
     * @return int
     */
    public function toUTCTimestamp(string $date)
    {
        return Date::toUTCTimestamp($date, $this->getTimezoneCode());
    }
    
    /**
     * @param int $timestamp
     * @param string $format
     * @return string
     */
    public function fromUTCTimestamp(int $timestamp, string $format = Date::DEFAULT_TO_STRING_FORMAT)
    {
        return Date::fromUTCTimestamp($timestamp, $this->getTimezoneCode(), $format);
    }

    /**
     * @return string
     */
    public function getTimezoneCode()
    {
        return (new Timezone)->getTimezoneById($this->timezone_id);
    }

    /**
     * @return OtsProject|mixed|null
     */
    public function getCurrentProject($field = null)
    {
        return !$this->current_project ? null :
            ($field ? $this->current_project->{$field} : $this->current_project);
    }

    /**
     * @param $current_project
     */
    public function setCurrentProject($current_project)
    {
        $this->current_project = $current_project;
    }
}
