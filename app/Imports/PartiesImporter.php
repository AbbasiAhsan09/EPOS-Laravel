<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Parties;
use App\Models\PartyGroups;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PDO;

class PartiesImporter implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {
            if(array_key_exists("party_name", $row) && array_key_exists('party_phone', $row) 
        && array_key_exists('party_email', $row) && array_key_exists('city', $row) && array_key_exists('address', $row) 
    && array_key_exists('party_business', $row) && array_key_exists('party_group', $row))
        {
        
        $group = PartyGroups::where("group_name",$row["party_group"])->first();
        if(!$group){
            throw "Invalid Group";
        }
        
        $party = new Parties();
        $party->group_id = $group->id;
        $party->party_name = $row["party_name"];
        $party->email = $row["party_email"] ?? "-";
        $party->phone = $row["party_phone"] ?? "0";
        $party->website = $row["website"] ?? "";
        $party->location = $row["address"] ?? "";
        $party->business_name = $row["party_business"] ?? "";

        if(isset($row["city"]) && !empty($row["city"]))
        {
            $city = City::where("name", $row["city"])
            ->where("country_id","!=", 102)
            ->with("country","state")->first();
            if($city && $city->country && $city->state){
                    $party->city = $city->id;
                    $party->country = $city->country->id;
                    $party->state = $city->state->id;
            }
        }
        
        $party->store_id =  Auth::user()->store_id;
        $party->save();

        return $party;
        }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
