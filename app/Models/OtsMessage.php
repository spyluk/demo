<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class OtsMessage extends Model
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'main_id',
        'user_id',
        'site_id',
        'type_id',
        'subject',
        'message',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function statistic()
    {
        return $this->hasOne('App\Models\OtsMessageStatistic', 'main_id');
    }

    /**
     * @param int $message_id
     * @param int $user_id
     * @return array|null
     */
    public function getMainMessageByIdAndUser(int $message_id, int $user_id)
    {
        $result = $this->getInitDbByFields(['id' => $message_id])
            ->select('x.*')
            ->leftJoin('ots_message_users as mu', 'mu.message_id', '=', 'x.id')
            ->where('mu.user_id', $user_id)
            ->whereNull('x.main_id')
            ->first();

        return $result ? $result->toArray() : null;
    }

    /**
     * @param int $message_id
     * @param int $user_id
     * @param int $page
     * @param int $per_page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginationMessageThreadByIdAndUser(int $message_id, int $user_id, int $page = 1, $per_page = 100)
    {
        return $this->getQueryThreadByIdAndUser($message_id, $user_id)
            ->paginate($per_page, ['*'], '', $page);
    }

    /**
     * @param int $message_id
     * @param int $user_id
     * @return mixed
     */
    public function getMessageByIdAndUser(int $message_id, int $user_id)
    {
        return $this->getQueryByIdAndUser($message_id, $user_id)->first();
    }

    /**
     * @param int $user_id
     * @param array $tags
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function getList(int $user_id, $tags = [], int $page = 1, $per_page = 10)
    {
        $columns = ['x.*', 'ms.count', 'mulastread.read', 'mulastread.read_at', 'ms.last_at'];
        $query = $this->getInitDbByFields()
            ->leftJoin('ots_message_users as mu', 'mu.message_id', '=', 'x.id')
            ->leftJoin('ots_message_statistics as ms', 'ms.main_id', '=', 'x.id')
            ->leftJoin('ots_message_users as mulastread', function($query) use ($user_id){
                $query->where('mulastread.message_id','=', DB::Raw('ms.last_id'));
                $query->where('mulastread.user_id','=',$this->quote($user_id));
            })
            ->where('mu.user_id', $user_id)
            ->whereNull('x.main_id')
            ->orderBy('ms.last_at', 'desc');

        if($tags) {
            $query->leftJoin('ots_message_user_tags as mut', function($query) use ($user_id){
                $query->where('mut.message_id', '=', DB::Raw('x.id'))
                    ->where('mut.user_id', '=', $this->quote($user_id));
            })->leftJoin('custom_var_codes as cvcode', 'cvcode.var_id', '=', 'mut.tag_id')
            ->leftJoin('custom_var_models as cvm', 'cvm.var_id', '=', 'mut.tag_id')
                ->whereIn('cvcode.code', $tags)
                ->where(function ($query) use ($user_id){
                    $query->whereNull('cvm.id')
                        ->orWhere(function ($query) use ($user_id){
                            $query->where('cvm.model_id', '=', $this->quote($user_id))
                                ->where('cvm.model_type', '=', $this->quote(User::class));
                        });
                });

        }

        return $query->select($columns)->paginate($per_page, ['*'], '', $page);
    }

    /**
     * @param int $message_id
     * @param int $user_id
     * @return array|null
     */
    public function getMessageThreadByIdAndUser(int $message_id, int $user_id)
    {
        $result = $this->getQueryThreadByIdAndUser($message_id, $user_id)
            ->get();

        return $result ? $result->toArray() : null;
    }

    /**
     * @param $message_id
     * @param $user_id
     * @return QueryBuilder
     */
    protected function getQueryThreadByIdAndUser($message_id, $user_id)
    {
        return $this->getInitDbByFields()
            ->select('x.*', 'mu.read', 'mu.read_at', 'u.first_name', 'u.last_name', 'u.id as user_id')
            ->leftJoin('ots_message_users as mu', 'mu.message_id', '=', 'x.id')
            ->leftJoin('users as u', 'x.user_id', '=', 'u.id')
            ->where('mu.user_id', $user_id)
            ->where(function($query) use ($message_id){
                $query->where(function($query) use ($message_id){
                    $query->where('x.id', '=', $message_id)
                        ->whereNull('x.main_id');
                })->orWhere('x.main_id', '=', $message_id);
            })->orderBy('x.created_at');
    }

    /**
     * @param $message_id
     * @param $user_id
     * @return QueryBuilder
     */
    protected function getQueryByIdAndUser($message_id, $user_id)
    {
        return $this->getInitDbByFields()
            ->select('x.*', 'mu.read', 'mu.read_at', 'u.first_name', 'u.last_name', 'u.id as user_id')
            ->leftJoin('ots_message_users as mu', 'mu.message_id', '=', 'x.id')
            ->leftJoin('users as u', 'x.user_id', '=', 'u.id')
            ->where('mu.user_id', $user_id)
            ->where('x.id', $message_id);
    }
}
