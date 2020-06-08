<?php
/**
 *
 * User: sergei
 * Date: 12.08.18
 */

namespace App\Forms;

use App\Models\OtsProject;
use App\Services\Ots\ProjectService;
use App\Validators\Forms\RegistrationValidator;
use App\Models\User as UserModel;
use App\Models\Country as CountryModel;
use App\Models\Timezone as TimezoneModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use \App\Events\User\RegistrationEvent;

class RegistrationForm
{
    /**
     * @param array $data
     * @param int $site_id
     * @param string $ip
     * @param int $language_id
     * @param array $tags
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws ValidationException
     * @throws \Exception
     */
    public function register(array $data, int $site_id, string $ip, int $language_id)
    {
        $data['site_id'] = $site_id;
        $data['language_id'] = $language_id;

        if(($country = $this->getCountryByIp($ip)))
        {
            $data['country_id'] = $country->id;
        }

        if(($timezone = $this->getTimezoneByIp($ip))) {
            $data['timezone_id'] = $timezone->id;
        }

        try {
            $validate = (new RegistrationValidator($data))->validate();
        } catch (ValidationException $e) {
            throw $e;
        }

        $validate['uid'] = Hash::make($data['email'] . time());
        $validate['confirmation_code'] = Str::random(30);
        $validate['password'] = Hash::make($validate['password']);

        $defaultProject = (new OtsProject())->getItemByFields(['site_id' => $site_id, 'default' => true]);

        if(!$defaultProject) {
            throw new \Exception('Not set default project.');
        }

        try {
            DB::beginTransaction();
                $user = (new UserModel)->create($validate);
                /*Register user in default project*/
                (new ProjectService())->addUser($defaultProject->id, $user->id);

                event(new RegistrationEvent($user));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return $user;
    }

    /**
     * @param $ip
     * @return CountryModel|null
     */
    protected function getCountryByIp($ip)
    {
        $country = null;
        try {
            $codeIso2 = geoip($ip)->country->isoCode;
            $country = (new CountryModel)->getCountryByIso2($codeIso2);
        } catch (\Exception $e) {
        }

        return $country;
    }

    /**
     * @param $ip
     * @return TimezoneModel|null
     */
    protected function getTimezoneByIp($ip)
    {
        $timezoneId = null;
        try {
            $timeRegion = geoip($ip)->location->timeZone;
            $timezoneId = (new TimezoneModel)->getTimezoneByTimeRegion($timeRegion);
        } catch (\Exception $e) {
        }

        return $timezoneId;
    }
}
