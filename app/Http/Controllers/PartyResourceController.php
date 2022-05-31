<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class PartyResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //search to see if the current user is in a parties
        $party = Auth::user()->party;

        if(!$party){
            //user is not in a party
            //show the view to create or join a party
            return view('dashboard.party.create');
        }
        else{
            //user is in a party, display it
            return redirect()->route('party.show', ['party' => $party->id]);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $user = User::find(Auth::id());
        //if user is in party
        if(isset($user->party_id)){
            //show the party instead of being able to create or join one
            return redirect()->route('party.show', ['party' => $user->party_id]);
        }

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

        $hideHostLib;
        if($request->hideHostLib == "2"){
            $hideHostLib = true; //we want to hide lib to party guests
        }
        else{
            $hideHostLib = false; //show as a default as well
        }

        $party = Party::create([
            'partyName' => $request->partyName,
            'host_id' => $userID,
            'password' => Hash::make($request->password),
            'hideHostLibrary' => $hideHostLib,
            'isLocked' => false, //unlocked by default
        ]);


        //add user in party relationship
        $user = User::find($userID);

        $party->users()->save($user);

        //allow spotify top load the users library
        session(['spotifyApiUserId' => $userID]);       
        
        //return the party page showing the info for this party
        return redirect()->route('party.show', ['party' => $party->id]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Party  $party
     * @return \Illuminate\Http\Response
     */
    public function show($party)
    {        
        $party = Party::find($party);

        //if no party, go to party.create
        if(!isset($party)){
            return redirect()->route('party.create');

        }

        return view('dashboard/party/show', ['party' => $party]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Party  $party
     * @return \Illuminate\Http\Response
     */
    public function edit($partyid)
    {
        //find the party to edit
        $party = Party::find($partyid);

        //verufy the user is the host of the party
        if(!isset($party)){
            return redirect()->route('party.create');

        }
        //else return the view to edit a party and pass aloing the aprty
        return view('dashboard/party/edit', ['party' => $party]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Party  $party
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $partyid)
    {
        //update the party info
            //name
            //password
            //hostLib
        $party = Party::find($partyid);

        //if the username hasnt changed
            //dont validate the username within the DB
        if($request->partyName == $party->partyName){
            $request->validate([
                'partyName' => ['required', 'string', 'max:60'],
                'password' => [ 'confirmed'],
            ]);
        }
        else{
            //else the username has changed and needs checking
            $request->validate([
                'partyName' => ['required', 'string', 'max:60', 'unique:parties'],
                'password' => [ 'confirmed'],
            ]);
        }      

        $hideHostLib;
        if($request->hideHostLib == "2"){
            $hideHostLib = true; //we want to hide lib to party guests
        }
        else{
            $hideHostLib = false; //show as a default as well
        }

        //update the party with the correct info
        //if the party name has changed
        if($request->partyName != $party->partyName){
            $party->partyName = $request->partyName;
        }

        //if the password from the request is not null
        if(isset($request->password)){
            //if the new password and old one do not match
            if(!Hash::check($request->password, $party->password)){
                //passwords are different, save a hash of the new one
                $party->password = Hash::make($request->password);
            }
            //else the passwords match ands there is no need to change it
        }//else there was no password in the input

        $party->hideHostLibrary = $hideHostLib;

        $party->save();

        //redirect to party.show
        return redirect()->route('party.show', ['party' => $party->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Party  $party
     * @return \Illuminate\Http\Response
     */
    public function destroy($partyID)
    {
        Party::destroy($partyID);

        //route to the create party page
        return redirect()->route('party.index');

    }
}
