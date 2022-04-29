<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;


class PartyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //search to see if the current user is in a parties
        $userid = Auth::id();

        $user = User::find($userid);

        $party = $user->party;

        if(!$party){
            //user is not in a party
            //show the view to create or join a party
            return view('dashboard.party.create');
        }
        else{
            //user is in a party, 
            //pass it to a view to show poarty info
            //and allow user to leave party
            return redirect()->route('party.show', ['party' => $party->id]);

        }
        //dd($user->party);
        //return view('dashboard.party.create');
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
        
        //dd($party->id);
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
        //use middleware to ensure someone is in a party before showing
        //fetch the party form the db
        
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
        //dd($partyid);
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

        //dd($request->partyName, $request->password, $request->hideHostLib);

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

        
        //dd($party);
    }

    //function to join a user to a party
    public function join(Request $request) {
        //validate info
        $request->validate([
            'partyName' => ['required', 'string', 'max:60', 'exists:parties'],
            'password' => ['required' ],
        ]);

        //if a party exists with that name
        $party = Party::where('partyName', $request->partyName)->get();
        //dd($party[0]->password);
        //$partyPwd = $party->get('partyName');
        //$partyid 
        //dd($party->modelKeys());


        if(isset($party)){
            //if the password in the request matches the hashed one in the db
            if(Hash::check($request->password, $party[0]->password)){
                //add user to the party
                $userID = Auth::id(); 
                $user = User::find($userID);

                $party[0]->users()->save($user);

                //if the party allows the user to view the hosts lib
                if(!$party[0]->hideHostLibrary){
                    //not hiding, set session var
                    session(['spotifyApiUserId' => $party[0]->host_id]);
                }

                return redirect()->route('party.show', ['party' => $party[0]->id]);
            }
            else{
                throw ValidationException::withMessages([
                    'password' => __('auth.password'),
                ]);
            }
        }
        else{
            //no party found return error
        }

        
        

    }

    //function to remove a user from the party
    public function leave(Request $request) {
        $userToRemove = $request->leaveButton;

        //update the users [party_id] to null
        $user = User::find($userToRemove);

        $user->party_id = null;

        $user->save();

        return redirect()->route('party.index');

        //return to the party page
        //if the hsot is kicking a user, go back to the party home page

        //if a user is le;aving a party, go to the create pagew

        //both of these are done by8 gpoing to index

        //dd($request->kickButton);
        //fetch the user 
    }
}
