<?php

namespace App\Http\Controllers;

use App\Models\PartyGroups;
use Illuminate\Http\Request;

class PartyGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = PartyGroups::all();
        return view('parties.party_group.index',compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
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
            $group = new PartyGroups();
            $group->group_name = $request->group_name;
            $group->save();

            toast('Group Added!','success');
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PartyGroups  $partyGroups
     * @return \Illuminate\Http\Response
     */
    public function show(PartyGroups $partyGroups)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PartyGroups  $partyGroups
     * @return \Illuminate\Http\Response
     */
    public function edit(PartyGroups $partyGroups)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PartyGroups  $partyGroups
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        try {
            $group =  PartyGroups::find($id);
            $group->group_name = $request->group_name;
            $group->save();

            toast('Group Update!','info');
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PartyGroups  $partyGroups
     * @return \Illuminate\Http\Response
     */
    public function destroy(PartyGroups $partyGroups)
    {
        //
    }
}
