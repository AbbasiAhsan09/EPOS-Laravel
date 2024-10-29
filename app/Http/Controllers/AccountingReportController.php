<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Parties;
use App\Models\PartyGroups;
use Illuminate\Http\Request;

class AccountingReportController extends Controller
{
    public function customer_payments(Request $request) {
        try {
            
            $data = AccountTransaction::whereHas("account", function($accountQry){
                $accountQry->where("reference_type",'customer')->orWhere("account_number",1000);
            })->with("account","source_account_detail")
            ->where("credit",">",0)->get();

            $customers = Parties::where("group_id", PartyGroups::where("group_name","like","%customer%")->first()->id)->filterByStore()->get(); 
            
            // dd($data);
            return view("reports.accounts.customer-payments", compact("data","customers"));

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
