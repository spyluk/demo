<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Eloquent\SoftDeletes;
use App\Models\Traits\BaseTrait;

class Site extends Model
{
    use BaseTrait, SoftDeletes;
    /**
     * @var integer
     */
    const DEFAULT_SITE  = 1;
    /**
     * @var integer
     */
    const TEST = 2;

    /**
     * @var array
     */
    protected $site_ids = [];
    /**
     * @var array
     */
    protected $sites = [];

    /**
     * @param $domain
     * @return array
     */
    public function getSiteByDomain($domain)
    {
        if (!isset($this->site_ids[$domain])) {
            $return = self::where('domain', $domain)
                ->first();

            if ($return) {
                $this->setSiteByDomain($domain, $return);
            }
        } else {
            $return = $this->sites[$this->site_ids[$domain]];
        }

        return $return;
    }

    /**
     * @param $id
     * @param $site
     */
    protected function setSiteById($id, $site)
    {
        if ($id && $site) {
            $this->site_ids[$site['domain']] = $id;
            $this->sites[$id] = $site;
        }
    }

    /**
     * @param $domain
     * @param $site
     */
    protected function setSiteByDomain($domain, $site)
    {
        if ($domain && $site) {
            $this->site_ids[$domain] = $site['id'];
            $this->sites[$site['id']] = $site;
        }
    }
}
