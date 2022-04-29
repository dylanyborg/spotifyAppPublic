<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 


class EnsureSpotifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //make sure the user is in a party
        $user = User::find(Auth::id());

        //dd($user->party->host_id);

        /*
            use this middleware to ensure that the user has oermission to 
            access/view certain libraries

            if the user is not in a party
                then the user cannot do anything spotify related
                this is because queue will always act on the host
                so there is no need for the user to access the spotify lib
                wiht no intentions of queueing a song
            
            user is in a party
                if the sesison var for user lib is set to the current user
                    ensure the user has a access token
                if the session var is set to the host
                    ensure the user has access to the hosts libraries
        */
        //if the user is not in a party
        if(!isset($user->party_id)){
            //redirect to the join party page
            return redirect()->route('party.index');
        }
        
        //make sure partyLocked (bool) isnt true
        if($user->party->isLocked){
            //user cannot access party privelages
            return redirect()->route('party.index');
        }
        
        //else if user is in party, ensure it is not a locked party
            //aka hsot of party hasnt locked it
        
        
        
            /*
        else{//else user is in a party
            //if the party is set to privateLibrary
            if($user->party->hideHostLibrary){ //hideHostLib is a bool
                //user cannot view the hosts library
                //ensure the user has a spotify account linked 
                //if user has no account linked
                if(isset($user->spotifyUserAccessToken)){
                    //user can only search for songs

                }

            }//else the user can view the host library
        }
        //ensure the user has an api set to use
        session(['spotifyApiUserId' => ])
        */

        return $next($request);
    }
}
