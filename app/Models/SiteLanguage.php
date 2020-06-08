<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Eloquent\SoftDeletes;
use App\Models\Traits\BaseTrait;

class SiteLanguage extends Model
{
    use BaseTrait;

    /**
     * @var null
     */
    protected $language_by_code = null;
    /**
     * @var array
     */
    protected $site_language = [];

    /**
     * @param $site_id
     * @param $code
     * @return array
     */
    public function getSiteLanguagesByCode($site_id, $code)
    {
        $site_languages = $this->getSiteLanguages($site_id);
        return isset($site_languages[$code]) ?
            $site_languages[$code] :
            [];
    }

    /**
     * @param $site_id
     * @param $language_id
     * @return array|mixed
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
            $res = Language::whereIn('id', function ($query) use ($site_id) {
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
