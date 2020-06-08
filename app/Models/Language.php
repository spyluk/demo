<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class Language extends Model
{
    use BaseTrait;
    /**
     *
     */
    const ENGLISH = 1;
    /**
     *
     */
    const RUSSIAN = 2;

    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var array
     */
    protected $site_language = [];
    /**
     * @var array
     */
    protected $language_by_code = [];

    /**
     * @param $code
     * @return Language
     */
    public function getLanguageByCode($code)
    {
        $return = self::where('code', $code)
            ->first();

        return $return;
    }

    /**
     * @param $site_id
     * @param $language_id
     * @return Language|mixed
     */
    public function getSiteLanguageByLanguageId($site_id, $language_id)
    {
        $site_languages = $this->getSiteLanguages($site_id);
        if(isset($this->language_by_code[$language_id])) {
            return isset($site_languages[$this->language_by_code[$language_id]]) ?
                $site_languages[$this->language_by_code[$language_id]] :
                [];
        }

        return [];
    }

    /**
     * @param $site_id
     * @return array
     */
    public function getSiteLanguages($site_id)
    {
        if(!isset($this->site_language[$site_id])) {
            $res = self::whereIn('id', function ($query) use ($site_id) {
                    $query
                        ->select('language_id')
                        ->from('site_languages')
                        ->where('site_id', $site_id);
                })
                ->get()->all();

            if($res) {
                foreach ($res as $language) {
                    $this->language_by_code[$language->id] = $language->code;
                    $this->site_language[$site_id][$language->code] = $language;
                }
            } else {
                $this->site_language[$site_id] = null;
            }
        }

        return $this->site_language[$site_id];
    }
}
