<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifyLibraryToView
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
        //make sure the user has acces to either their own spotify lib
        //or that the party host lib is available for viewing

        $user = User::find(Auth::id());

        //if the user has not connected to spotify
        if (!isset($user->spotifyUserAccessToken)) {
            //if no access to host library
            if($user->party->hideHostLibrary){
                //user can only search for songs
                return redirect()->route('search.index');
            }
            
        }

        return $next($request);
    }
}
