<div class="row">
    <div class="col">
        <label for="country">Country</label>
        <div class="input-group input-group-outline">
        <select wire:model="selectedCountry" class="form-control" name="country">
            <option value="">Select Country</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
            @endforeach
        </select>
        </div>
    </div>

        <div class="col">
        <label for="state">State</label>
            <div class="input-group input-group-outline">
            <select wire:model="selectedState" class="form-control" name="state">
                <option value="">Select State</option>
                @if(!is_null($selectedCountry))

                @foreach($states as $state)
                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
                @endif

            </select>
            </div>
        </div>

        <div class="col">
        <label for="city">City</label>
            <div class="input-group input-group-outline">
            <select wire:model="selectedCity" class="form-control" name="city">
                <option value="">Select City</option>
                @if(!is_null($selectedState))
                @foreach($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
                @endif

            </select>
            </div>
        </div>
</div>
