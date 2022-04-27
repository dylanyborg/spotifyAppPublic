<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class PartyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('dashboard.party.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.party.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        //validate info
        $request->validate([
            'partyName' => ['required', 'string', 'max:60', 'unique:parties'],
            'password' => ['required', 'confirmed'],
        ]);

        $userID = Auth::id();
        $party = Party::create([
            'partyName' => $request->partyName,
            'host_id' => $userID,
            'password' => Hash::make($request->password),
        ]);

        
        
        dd($party->id);
        //return the party page showing the info for this party
        return redirect()->route('dashboard/party/', ['party' => $party->id]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Party  $party
     * @return \Illuminate\Http\Response
     */
    public function show($party)
    {
        //use middleware to ensure someone is in a party before showing
        //fetch the party form the db
        
        $party = Party::find($party);

        return view('dashboard/party/show', ['party' => $party]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Party  $party
     * @return \Illuminate\Http\Response
     */
    public function edit($party)
    {
        //
        dd($party);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Party  $party
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Party $party)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Party  $party
     * @return \Illuminate\Http\Response
     */
    public function destroy(Party $party)
    {
        //
    }
}
