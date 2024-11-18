<?php

namespace App\Http\Livewire;

use App\Models\Parties;
use Livewire\Component;

class PartyDropdown extends Component
{

    public $classes = 'form-control', $name = 'party_id', $selected_id = null, $showCustomer = true, $showVendor = true, $parties = [];
    protected $listeners = ['party_added' => 'update_dropdown'];
    
    public function render()
    {
        
        return view('livewire.party-dropdown');
    }

    public function mount()
    {
        $this->loadPartiesList();
    }

    public function loadPartiesList()
    {
        $allParties = Parties::filterByStore()
            ->orderBy('party_name', 'ASC')
            ->with('groups');

        // Apply conditions based on `showCustomer` and `showVendor`
        if ($this->showCustomer && !$this->showVendor) {
            $allParties->where('party_group_id', 1);
        } elseif (!$this->showCustomer && $this->showVendor) {
            $allParties->where('party_group_id', 2);
        }

        // Retrieve the results and group them by `group_name`
        $allParties = $allParties->get()->groupBy(function ($party) {
            return optional($party->groups)->group_name;
        });

        // Transform the grouped data for the dropdown
        $this->parties = $allParties->map(function ($groupedParties, $groupName) {
            return [
                'group_name' => $groupName,
                'parties' => $groupedParties->map(function ($party) {
                    return [
                        'id' => $party->id,
                        'party_name' => $party->party_name,
                    ];
                })->toArray(),
            ];
        })->values()->toArray();
    }



    public function update_dropdown($data) {
        $this->mount();
        $this->selected_id = $data["id"];
    }
}
