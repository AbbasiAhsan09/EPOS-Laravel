<?php

namespace App\Http\Controllers;

use App\Models\Parties;
use App\Models\PartyGroups;
use Illuminate\Http\Request;

class PartiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($group_id = null)
    {
        try {
        if(!$group_id){
            $party_groups = PartyGroups::all();
            $parties = Parties::orderby('group_id','DESC')->paginate(20);
            
            return view('parties.index',compact('party_groups','parties','group_id'));
        }

        $party_groups = PartyGroups::all();
        $parties = Parties::orderby('group_id','DESC')->where('group_id',$group_id)->paginate(20);
        return view('parties.index',compact('party_groups','parties','group_id'));
        
        } catch (\Throwable $th) {
            //throw $th;
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
            $party->country = $request->country;
            $party->city = $request->city;
            $party->website = $request->website;
            $party->group_id = $request->group_id;
            $party->location = $request->location;
            $party->save();

            
            toast('Party Added!','success');
            return redirect()->back();


        } catch (\Throwable $th) {
            //throw $th;
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
    public function update(Request $request, Parties $parties)
    {
        //
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
}
