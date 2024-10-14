<?php

namespace App\Http\Controllers;

use App\Models\Labour;
use App\Models\LabourWorkHistory;
use Illuminate\Http\Request;

class LabourWorkHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = [];

        return view("labour.work-history.index",compact('items'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $labours = Labour::filterByStore()->orderBy('name','ASC')->get();
        $history  = null;
        return view("labour.work-history.form",compact('labours','history')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LabourWorkHistory  $labourWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function show(LabourWorkHistory $labourWorkHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LabourWorkHistory  $labourWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(LabourWorkHistory $labourWorkHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LabourWorkHistory  $labourWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LabourWorkHistory $labourWorkHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LabourWorkHistory  $labourWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(LabourWorkHistory $labourWorkHistory)
    {
        //
    }
}
