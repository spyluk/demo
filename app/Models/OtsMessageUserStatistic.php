<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;
use Illuminate\Support\Facades\DB;

class OtsMessageUserStatistic extends Model
{
    use BaseTrait;

    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'site_id',
        'tag_id',
        'count',
        'count_read',
    ];

    /**
     * @param $user_id
     * @param $site_id
     * @param null $tag_id
     * @return array|\Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getByUserAndSite($user_id, $site_id, $tag_id = null)
    {
        return $this->getItemByFields(['user_id' => $user_id, 'site_id' => $site_id, 'tag_id' => $tag_id]);
    }

    /**
     * @param $user_id
     * @param $site_id
     * @return \Illuminate\Support\Collection
     */
    public function getTagsByUserAndSite($user_id, $site_id, $main = false)
    {
        $statistic = $this->getInitDbByFields(['x.user_id' => $user_id, 'x.site_id' => $site_id])
            ->select('x.*', 'cvcode.code as tag')
            ->leftJoin('custom_var_codes as cvcode', 'cvcode.var_id', '=','x.tag_id');

        if($main) {
            $statistic->whereNull('cvcode.code');
        }

        return $statistic->get();
    }

    /**
     * @param $user_id
     * @param $site_id
     * @param null $tag_id
     * @return Model|null
     */
    public function increaseUserSiteTagStatistic($user_id, $site_id, $tag_id = null)
    {
        return $this->updateOnExists(
            ['user_id' => $user_id, 'site_id' => $site_id, 'tag_id' => $tag_id],
            ['count_read' => DB::Raw('count_read+1')]
        );
    }
}
