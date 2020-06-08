<?php

namespace App\Providers\Auth;

/**
 * 
 * User: sergei
 * Date: 23.11.18
 * Time: 18:24
 */
use Illuminate\Auth\EloquentUserProvider as UserProvider;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;


class CustomEloquentUserProvider extends UserProvider {
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                array_key_exists('password', $credentials))) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->createModel()->newQuery();
        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }

            $query->where('active', 1);
        }

        return $query->first();
    }
}