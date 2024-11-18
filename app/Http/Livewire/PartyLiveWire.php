<?php

namespace App\Http\Livewire;

use App\Helpers\ConfigHelper;
use App\Http\Controllers\PartiesController;
use App\Models\Parties;
use App\Models\PartyGroups;
use Livewire\Component;

class PartyLiveWire extends Component
{
    public $party_groups = [];
    public $party_name = '', $group_id = null, $opening_balance = 0, $email, $phone, $business_name, $location;

    protected $rules = [
        'party_name' => 'required|string|max:255',
        'group_id' => 'required|integer'
    ];
    public function render()
    {
        $this->party_groups = PartyGroups::get();

        return view('livewire.party-live-wire');
    }


    public function add_party(){
        try {
            $this->validate();


            $party = new Parties();
            $party->party_name = $this->party_name;
            $party->email = $this->email;
            $party->phone = $this->phone;
            $party->business_name = $this->business_name;
            
            if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                $party->opening_balance = $this->opening_balance ?? 0;
            }

            $party->group_id = $this->group_id;
            $party->location = $this->location;
            $party->save();            
            
            if($party && ConfigHelper::getStoreConfig()["use_accounting_module"]){
                PartiesController::create_party_account($party->id);
            }
            
            toast('Party Added!','success');

            $this->reset([
                'party_name',
                'group_id',
                'location',
                'business_name',
                'phone',
                'email',
                'opening_balance'
            ]);

            $this->emit("party_added",['id' => $party->id]);


        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
