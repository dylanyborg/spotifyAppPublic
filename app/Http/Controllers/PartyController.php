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
    
    //function to join a user to a party
    public function join(Request $request) {
        //validate info
        $request->validate([
            'partyName' => ['required', 'string', 'max:60', 'exists:parties'],
            'password' => ['required' ],
        ]);

        //if a party exists with that name
        $party = Party::where('partyName', $request->partyName)->get();

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
        //if the host is kicking a user, go back to the party home page

        //if a user is le;aving a party, go to the create pagew

        //both of these are done by going to party.index 
    }

    //fucntion to lock the party from api requests
    public function lock(Request $request) {
        //set the attribute in the party table (isLocked) to the negation (!)
        $party = Party::find($request->lockButton);

        $party->isLocked = !$party->isLocked;

        $party->save();

        return redirect()->route('party.index');
    }
}
