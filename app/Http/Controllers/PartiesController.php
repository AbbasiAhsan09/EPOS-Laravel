<?php

namespace App\Http\Controllers;

use App\Imports\PartiesImporter;
use App\Models\Parties;
use App\Models\PartyGroups;
use Illuminate\Http\Request;
use Excel;

class PartiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

        $party_groups = PartyGroups::all();
        $parties = Parties::with("groups")
        ->when($request->has('party_group'), function($q) use ($request) {
            $q->where("group_id", $request->party_group);
        })
        ->when(
            function ($query) use ($request) {
                return $request->filled('party_name') || $request->filled('party_phone') || $request->filled('party_email');
            },
            function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    if ($request->filled('party_name')) {
                        $query->where("party_name", 'like', '%' . $request->party_name . '%');
                    }
                    if ($request->filled('party_phone')) {
                        $query->orWhere("phone", 'like', '%' . $request->party_phone . '%');
                    }
                    if ($request->filled('party_email')) {
                        $query->orWhere("email", 'like', '%' . $request->party_email . '%');
                    }
                });
            }
        )
        ->orderBy('group_id', 'DESC')
        ->byUser()
        ->paginate(20)
        ->withQueryString();
    
        $group_id = $request->has('party_group') ? $request->party_group : false;
        
        return view('parties.index',compact('party_groups','parties','group_id'));
        
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // dd($request);
            $party = new Parties();
            $party->party_name = $request->party_name;
            $party->email = $request->email;
            $party->phone = $request->phone;
            $party->business_name = $request->business_name;
            if($request->has('country') && $request->country){
                $party->country = $request->country;
            }
            if($request->has('city') && $request->city){
                $party->city = $request->city;
            }
            if($request->has('state') && $request->state){
                $party->state = $request->state;
            }
            // $party->city = $request->city;
            // $party->state = $request->state;
            $party->website = $request->website;
            $party->group_id = $request->group_id;
            $party->location = $request->location;
            $party->save();

            
            toast('Party Added!','success');
            return redirect()->back();


        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Parties  $parties
     * @return \Illuminate\Http\Response
     */
    public function show(Parties $parties)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Parties  $parties
     * @return \Illuminate\Http\Response
     */
    public function edit(Parties $parties)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Parties  $parties
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        try {
            // dd($request->all());
            $party =  Parties::where('id',$id)->byUser()->first();
            $party->party_name = $request->party_name;
            $party->email = $request->email;
            $party->phone = $request->phone;
            $party->business_name = $request->business_name;
            if($request->has('country') && $request->country){
                $party->country = $request->country;
            }
            if($request->has('city') && $request->city){
                $party->city = $request->city;
            }
            if($request->has('state') && $request->state){
                $party->state = $request->state;
            }
            $party->website = $request->website;
            $party->group_id = $request->group_id;
            $party->location = $request->location;
            $party->save();

            
            toast('Party Updated!','info');
            return redirect()->back();


        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Parties  $parties
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parties $parties)
    {
        //
    }

    function importCSV(Request $request)  {
        try {
            // dd($request);
             Excel::import(new PartiesImporter, $request->file("file"));
            return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
