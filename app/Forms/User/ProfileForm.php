<?php
/**
 *
 * User: sergei
 * Date: 12.08.18
 * Time: 10:54
 */
namespace App\Forms\User;

use App\Models\User;
use App\Services\MediaService;
use App\Validators\Forms\User\UpdateValidator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileForm
{
    /**
     * @param int $user_id
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws ValidationException
     */
    public function update(int $user_id, array $data)
    {
        try {
            $validate = (new UpdateValidator($data))->validate();
        } catch (ValidationException $e) {
            throw $e;
        }

        if(!empty($validate['password']))
        {
            $validate['password'] = Hash::make($validate['password']);
        }

        $user = new User();
        if($validate) {
            $user->updateOrCreate(['id' => $user_id], $validate);
        }
        /**
         * @var $user User
         */
        $user = $user::find($user_id);
        if(!empty($validate['avatar'])) {
            (new MediaService)->replace($user, $validate['avatar'], User::MEDIA_AVATAR);
        }

        return $user;
    }
}