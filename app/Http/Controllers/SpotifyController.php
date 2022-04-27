<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\QueueSong;
use App\Models\User; //can update db usinf the user model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//use Illuminate\Support\Facades\Http;

require '../vendor/autoload.php';

//use src\SpotifyWebAPI;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController extends Controller
{


    public function login(){
        //run the api call to spotify to tri8gger a user to log into their spotify account
        /*
        $clientId = 'CLITNEID';
        $redirect_uri = 'http://localhost/callback';

        $state = 

        $response = Http::get('https://accounts.spotify.com/authorize', [
            'client_id' => $clientId,
            'response_type' => 'code',
             'redirect_uri' => $redirect_uri,
             'state' => 'state',
             'scope' => [
                'user-library-read',
                'user-read-playback-state'
             ],

        ]);
        */

        //dd('about to attempot spotify login');

        // USIONG THE SPOTIFY PHP WRAPPER FROPM JWILSSON GITHUB
        if(Auth::check()){ //if user is logged in
        
            $session = new Session(
                '1d53c77dcd0e4cb6b6191f74e2ce6c51',
                '18e12cd385514d91a4d0aebb75e01423',
                'http://localhost/callback'
            );

            $state = $session->generateState();
            $options = [
                'scope' => [
                    'playlist-read-private',
                    'user-read-playback-state',
                    'user-library-read',
                    'user-modify-playback-state',
                    'user-read-playback-state',
                    'user-read-currently-playing',

                ],
                'state' => $state,
            ];

            

            //save the state variable to be used in the callback
            session(['spotifyState' => $state]);

            //dd('pre sending request to spotify');


            return redirect()->away($session->getAuthorizeUrl($options));
        }

    }

    //function to handle the callback fomr spotify login request
    //spotify will return 
    public function callback(){
        //dd('callback successful');
        $userID;
        if(Auth::check()){ //if user is logged in
            $userID = Auth::id(); //get the userID
        }

        $session = new Session(
            '1d53c77dcd0e4cb6b6191f74e2ce6c51',
            '18e12cd385514d91a4d0aebb75e01423',
            'http://localhost/callback'
        );

        $state = $_GET['state'];

        $storedState = session('spotifyState');

        if($state !== $storedState){
            die('state mismatch');
        }

        //request access tokern using the code from spotiofy
        $session->requestAccessToken($_GET['code']);

        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

        //store the access and refresh tokens somewhere
        session(['userAccessToken' => $accessToken]);
        session(['userRefreshToken' => $refreshToken]);

        //save the access and refresh tokens in the db
        //return number of rows affected
        /*
        $affected = DB::table('users')
        ->where('id', $userID)
        ->update(['spotifyUserAccessToken' => $accessToken, 
                    'spotifyUserRefreshToken' => $refreshToken]);
        */

        //update user row using User Model
        $user = User::find($userID);

        $user->spotifyUserAccessToken = $accessToken;
        $user->spotifyUserRefreshToken = $refreshToken;

        $user->save();

        //create new spotifyWebApi and set the accessToken
        $api = new SpotifyWebAPI();

        $api->setAccessToken($accessToken);

        //dd($api->me());
        $usersName = $api->me()->display_name;

        //fetch users spotify lib
        //$tracks = $api->getMySavedTracks();

        //send the user along back to the app
        return redirect()->route('userLibrary.show');
        //return view('trackListViews/userLibrary', ['tracks' => $tracks]);

    }




    public function loadUserLibrary(Request $request){


        $userID;
        if(Auth::check()){ //if user is logged in
            $userID = Auth::id(); //get the userID
            
            //if all songs loaded, do nothing
            if(  $request->session()->has('userLibrary')){
                if(session('allUserLibLoaded') == 1){
                    //return to the lib page
                    return view('spotifyRemoteControl/userLibrary', ['tracks' => session('userLibrary')]);
                }
            }
            //@param userID is the current user
            //return the api to be used for spotifyWebApi calls
            $spotifyInfo = $this->getApi($userID);

            $spotifyApi = $spotifyInfo[0];
            $spotifySession = $spotifyInfo[1];

            //check if all songs loaded
            //check if any songs are loaded
            
            $existingNumberOfSongs = 0;
            $existingTracklist;
            //if the session variable for user lib has songs in it
            if($request->session()->has('userLibrary')){
                //number of existing songs will change
                $existingTracklist = $request->session()->get('userLibrary');
                $existingNumberOfSongs = count($existingTracklist);                   
            }
            //else song count stays zero
                        
            //load user tracks, using the number oif songs loaded as the offset
            //refresh the tokens

            $newTracks = $spotifyApi->getMySavedTracks([
                'limit' => 50,
                'offset' => $existingNumberOfSongs,
            ]);

            

            //refresh tokens in db/session ********
            $this->refreshTokens($spotifyApi, $spotifySession, $userID);

            $numberOfSongsLoaded = count($newTracks->items);
            $updatedTracks;
            //if there was existing songs, merge the new and existing array
            if($request->session()->has('userLibrary')){
                $updatedTracks = array_merge(session('userLibrary'), $newTracks->items);
                session(['userLibrary' => $updatedTracks]);
            }
            else{
                //save new tracks to a session var
                $updatedTracks = $newTracks->items;
                session(['userLibrary' => $newTracks->items]);
            }

            //check if more songs to load
            //if 50 songs were loaded
            if(count($updatedTracks) < 50){
                //no more songs to be loaded
                session(['allUserLibLoaded' => 1]);
            }
            else{
                //more songs to load
                session(['allUserLibLoaded' => 0]);

            }

            //dd($updatedTracks);
            //dd($updatedTracks);


            //return the userLib view and give the usersCurrentLib
            return view('spotifyRemoteControl/userLibrary', ['tracks' => $updatedTracks]);
        }

    }

    //function to freshen up thyer access and reftresh tokens
    //returns an API to use to make calls
    public function getApi($userID){
        $userSession = new Session(
            '1d53c77dcd0e4cb6b6191f74e2ce6c51',
            '18e12cd385514d91a4d0aebb75e01423'
        );

        //request tokens form the db
        /*
        $tokens = DB::table('users')
        ->select('spotifyUserAccessToken as accessToken', 'spotifyUserRefreshToken as refreshToken')
        ->where('id', '=', $userID)
        ->get();

        $accessToken = $tokens['accessToken'];
        $refreshToken = $tokens['refreshToken'];
        */
        //using the user model
        $user = User::find($userID);

        $accessToken = $user->spotifyUserAccessToken;
        $refreshToken = $user->spotifyUserRefreshToken;

        //dd($user);

        //use existing tokens
        if($accessToken){
            $userSession->setAccessToken($accessToken);
            $userSession->setRefreshToken($refreshToken);
        } 
        else{
            // Or request a new access token
            $userSession->refreshAccessToken($refreshToken);
        }
        
        $options = [
            'auto_refresh' => true,
        ];

        $userApi = new SpotifyWebAPI($options, $userSession);

        return [$userApi, $userSession];

        //return $userApi;
    }

    public function refreshTokens($api, $session, $userID){
        //fetch the user

        $user = User::find($userID);

        //update topkej info for user
        $user->spotifyUserAccessToken = $session->getAccessToken();
        $user->spotifyUserRefreshToken = $session->getRefreshToken();

        //save user
        $user->save();

    }

    public function queueSong(Request $request){
        //Log::debug('in queue song')
        //dd("in queue song function");
        //dd($trackid);
        //will be able to pass a param userid to queue songs to a certain user
        if(Auth::check()){ //if user is logged in
            $userid = Auth::id(); //get the userID
            
            $songid = $request->input('songid');
            //dd($songid);

            QueueSong::dispatch($songid, $userid);
        }

        return \Response::json("success");

        //return;


    }

    public function search(Request $request){
        //if the request does not have a searchBar variable
        if($request->filled('search')){
            //perform the search on the request
            $searchQuery = $request->search;

            //fetch the api for the correct user to execute the search on (host or user)
            $userID = Auth::id(); //this should change to add a way to use the host tokens

            $spotifyInfo = $this->getApi($userID);

            $spotifyApi = $spotifyInfo[0];
            $spotifySession = $spotifyInfo[1];


            $searchResults;
            try {
                //search for the first 10 results for artist and track
                $searchResults = $spotifyApi->search($searchQuery, 'track,artist', [
                    'limit' => 10,
                ]);
            } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                dd("error:", $e);
            }

            //refresh the tokens after using them
            
            $this->refreshTokens($spotifyApi, $spotifySession, $userID);

            //save the search query to a session variable
            session(['lastSearchQuery' => $searchQuery]);
            //save the results of the search into a session?
            session(['lastSearchResults' =>$searchResults]);

            //return the search results to the search view

            return view('spotifyRemoteControl/search',['searchResults' => $searchResults]);



            //dd("search queue requested", $request->search);

        }
        else{ //no search initaited, load search page with rpevioous search
            //if there is a previous search
            if($request->session()->has('lastSearchQuery')){
                //load the view using the previous results
                return view('spotifyRemoteControl/search',['searchResults' => session('lastSearchResults')] );
                //dd( "loading proevious search:",session('lastSearchQuery') );

            }
            else{//else if there is no previous search
                //load the page with just the search bar
                return view('spotifyRemoteControl/search');
            }

        }

        //dd($request->searchBar);

        return view('spotifyRemoteControl/search', ['searchResults' => $searchResults]);

    }

    //function to get spotiofy user information
    
}
