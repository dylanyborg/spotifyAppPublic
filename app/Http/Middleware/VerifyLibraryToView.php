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

        //if the hostparty is unavalable
            //if the user hadoes not have a lib to view
                //redirect
            //else show the user liub
        //else show the host lib

        if($user->party->hideHostLibrary){
            if (!isset($user->spotifyUserAccessToken)) {
                //no party or view
                return redirect()->route('search.index');
            }
            session(['spotifyApiUserId' => Auth::id()]);
        }
        //else the host lib can be viewed.
        //check if the user has atleast a spotify account to use

        //if the user has not connected to spotify
        if (!isset($user->spotifyUserAccessToken)) {
            //user can only search for songs
            return redirect()->route('search.index');
            
            
        }
        //else make the api the user


        return $next($request);
    }
}
