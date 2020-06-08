<?php
/**
 *
 * User: sergei
 * Date: 12.08.18
 * Time: 10:54
 */
namespace App\Forms;

use App\Models\User;
use App\Validators\Forms\LoginValidator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginForm
{
    /**
     * @param array $data
     * @return User
     * @throws ValidationException
     * @throws \Exception
     */
    public function login(array $data)
    {
        try {
            $validate = (new LoginValidator($data))->validate();
        } catch (ValidationException $e) {
            throw $e;
        }

        /**
         * @var $user User
         */
        $user = User::where('email', $validate['email'])->first();
        if(!$user || !Hash::check($validate['password'], $user->password))
        {
            throw new \Exception('Credential error.');
        }

        return $user;
    }
}