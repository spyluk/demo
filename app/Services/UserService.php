<?php
/**
 *
 * User: sergei
 * Date: 16.01.19
 * Time: 16:53
 */

namespace App\Services;

use App\Models\Language;
use App\Models\OtsProject;
use App\Models\Site;
use App\Models\User;
use App\Services\Variables\Lists\TagList;

class UserService
{
    /**
     *
     */
    const DEFAULT_LIST_USER_TAGS = 'list.tags.users';

    /**
     * @var User
     */
    protected $user;

    /**
     * Message constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @param $site_id
     * @param array $filter
     * @param int $page
     * @param array $with джойнить через with иначе будет проблема n+1
     * @param int $per_page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception
     */
    public function getList($site_id, $filter = [], int $page = 1, $with = [], $per_page = 10)
    {
        $additional_filter_data = [];
        $tags = $filter['tags'] ?? null;

        $models = [
            Site::class => $site_id,
            OtsProject::class => $this->user->getCurrentProject('id'),
            User::class => $this->user->id
        ];

        $tags = ($tags = new TagList($this->user->getCurrentProject(), $tags, Language::ENGLISH, $models)) ?
            $tags->getOnAllAvailable($this->user) : [];

        if(!$tags && !$this->user()->hasPermissionTo('management.users')) {
            throw new \Exception('Operation denied.');
        }

        if($tags) {
            $filter['tags'] = collect($tags)->pluck('id')->toArray();
            $additional_filter_data['tags']['user'] = $this->user();
        }

        $query = (new User())->getInitDbByFields($filter, $additional_filter_data);

        if($with) {
            $query->with($with);
        }

        return $query->paginate($per_page, ['x.*'], '', $page);
    }
}
