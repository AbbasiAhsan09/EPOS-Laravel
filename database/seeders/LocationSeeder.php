<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            // Read the JSON file
            $jsonData = file_get_contents(storage_path('app/data/location.json'));

            $data = json_decode($jsonData, true);

            if ($data && is_array($data)) {
                foreach ($data as $country) {
                    // Create Country
                    $newCountry = Country::create([
                        'name' => $country['name'],
                        'code' => $country['iso2']
                    ]);

                    if (isset($country['states']) && is_array($country['states'])) {
                        foreach ($country['states'] as $state) {
                            // Create State
                            $newState = State::create([
                                'country_id' => $newCountry->id,
                                'name' => $state['name'],
                                'code' => $state['state_code']
                            ]);

                            if (isset($state['cities']) && is_array($state['cities'])) {
                                foreach ($state['cities'] as $city) {
                                    // Create City
                                    City::create([
                                        'state_id' => $newState->id,
                                        'country_id' => $newCountry->id,
                                        'name' => $city['name'],
                                        // 'code' => 'citycode' // Replace with actual city code
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
    }
}
