<?php

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = str_getcsv(file_get_contents('https://raw.githubusercontent.com/lorey/list-of-countries/master/csv/countries.csv'), "\n");
        $iter=0;
        foreach($data as &$row) {
            if ($iter == 0) {
                $iter++;
                continue;
            }

            $row = str_getcsv($row, ";"); //parse the items in rows.

            $country = new Country;
            $country->alpha2 = $row[0];
            $country->alpha3 = $row[1];
            $country->name = $row[11];
            $country->numeric = $row[13];
            $country->code = intval($row[14]);
            $country->save();
        }
    }
}