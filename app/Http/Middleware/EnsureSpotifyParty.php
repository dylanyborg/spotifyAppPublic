<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EnsureSpotifyParty
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
        //make sure the user is in a party and that the party is not locked
        $user = User::find(Auth::id());

        //if the user is not in a party
        if(!isset($user->party_id)){
            //redirect to the join party page
            return redirect()->route('party.index');
        }
        //else the user is in a party
        //make sure partyLocked (bool) isnt true
        if($user->party->isLocked){
            //user cannot access party privelages
            return redirect()->route('party.index');
        }

        return $next($request);
    }
}
