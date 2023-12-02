<?php

namespace App\Http\Livewire;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Livewire\Component;

class Location extends Component
{
    public $selectedCountry;
    public $selectedState;
    public $selectedCity;
    public $countries;
    public $states;
    public $cities;

    public function mount($initialCountryId = null, $initialStateId = null, $initialCityId = null)
    {
        $this->countries = Country::all();
        $this->selectedCountry = $initialCountryId;
        $this->selectedState = $initialStateId;
        $this->selectedCity = $initialCityId;

        if ($initialCountryId) {
            $this->states = State::where('country_id', $initialCountryId)->get();
        }

        if ($initialStateId) {
            $this->cities = City::where('state_id', $initialStateId)->get();
        }
    }

    public function updatedSelectedCountry($value)
    {
        $this->states = State::where('country_id', $value)->get();
        $this->selectedState = null;
        $this->cities = [];
        $this->selectedCity = null;
    }

    public function updatedSelectedState($value)
    {
        $this->cities = City::where('state_id', $value)->get();
        $this->selectedCity = null;
    }

    public function render()
    {
        return view('livewire.location');
    }
}
