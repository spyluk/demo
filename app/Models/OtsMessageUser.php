<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class OtsMessageUser extends Model
{
    use BaseTrait;

    /**
     * @var string
     */
    protected $primaryKey = 'message_id';
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'message_id',
        'user_id',
        'read',
        'read_at',
    ];

    /**
     * @param array $ids
     * @return array
     */
    public function getUsersByMessageId(array $ids)
    {
        $return = $this->getInitDbByFields(['message_id' => $ids])
            ->select('x.*','u.first_name','u.last_name')
            ->leftJoin('users as u', 'u.id', '=', 'x.user_id')
            ->get();

        return ($return ? $return->toArray() : $return);
    }

    /**
     * @param $message_id
     * @param $user_id
     * @param $date
     * @return array
     */
    public function setUserRead($message_id, $user_id, $date)
    {
        return $this->updateOnExists(
            ['user_id' => $user_id, 'message_id' => $message_id],
            ['read' => 1, 'read_at' => $date]
        );
    }
}
