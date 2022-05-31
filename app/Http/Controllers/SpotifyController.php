<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\QueueSong;
use App\Models\User; //can update db usinf the user model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

require '../vendor/autoload.php';

//jwilsson spotify web API library for php
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController extends Controller
{


    public function login(){
        
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
                    'user-library-modify',
                    'user-modify-playback-state',
                    'user-read-playback-state',
                    'user-read-currently-playing',

                ],
                'state' => $state,
            ];

            //save the state variable to be used in the callback
            session(['spotifyState' => $state]);

            return redirect()->away($session->getAuthorizeUrl($options));
        }

    }

    //function to handle the callback for spotify login request
    //spotify login request returns to this route
    public function callback(){
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

        //request access tokern using the code from spotify
        $session->requestAccessToken($_GET['code']);

        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

        //store the access and refresh tokens somewhere
        session(['userAccessToken' => $accessToken]);
        session(['userRefreshToken' => $refreshToken]);

        //save the access and refresh tokens in the db
        //update user row using User Model
        $user = User::find($userID);

        $user->spotifyUserAccessToken = $accessToken;
        $user->spotifyUserRefreshToken = $refreshToken;

        $user->save();

        //create new spotifyWebApi and set the accessToken
        $api = new SpotifyWebAPI();

        $api->setAccessToken($accessToken);

        //allow spotify top load the users library
        session(['spotifyApiUserId' => $userID]);

        //show users lib to show it worked
        return redirect()->route('userLibrary.show');
    }

    //function to load a users library
    //only loads the first 50 songs
    public function loadUserLibrary(Request $request){

        //before loading the host library, make sure the party setting allow it
        $timeStart = microtime(true);

        $userID;
        if(Auth::check()){ //if user is logged in

            $apiUser;
            //get the user id of the correct api to use form a session
            //'spotifyApiUserId = the user_id of the current api to use (host or current user)
            if($request->session()->has('spotifyApiUserId')){
                $apiUser = session('spotifyApiUserId'); //will be user or host id
            }
            //this session var is set when logging in, connecting spotify, or joining party
            //if no session var is set, user cannot view any libraries or playlists
            else{
                //go to the search page
                return redirect()->route('search.index');
            }
            
            //return the api to be used for spotifyWebApi calls
            $spotifyInfo = $this->getApi($apiUser);

            $spotifyApi = $spotifyInfo[0];
            $spotifySession = $spotifyInfo[1];

            $existingNumberOfSongs = 0;
                        
            //load user tracks, using the number oif songs loaded as the offset

            $newTracks = $spotifyApi->getMySavedTracks([
                'limit' => 50,
                'offset' => $existingNumberOfSongs,
                'market' => 'US',
            ]);

            // save the number of laoderd songs to a session           
            $numberOfSongsLoaded = count($newTracks->items);
            session(['numOfSongsLoaded' => $numberOfSongsLoaded]);
            
            //check if more songs to load
            //if 50 songs were loaded
            if($numberOfSongsLoaded < 50){
                //no more songs to be loaded
                session(['allUserLibLoaded' => 1]);
            }
            else{
                //more songs to load
                session(['allUserLibLoaded' => 0]);

            }

            //fetch currently playing song
            $playbackInfo = $this->getCurrentlyPlayingTrack();

            //refresh tokens in db
            $this->refreshTokens($spotifyApi, $spotifySession, $apiUser);
            
            $timeEnd = microtime(true);

            $executionTime = $timeEnd - $timeStart;

            //dd($executionTime*1000);

            //return the userLib view and give the usersCurrentLib
            return view('spotifyRemoteControl/userLibrary')
            ->with('tracks', $newTracks)
            ->with('playbackInfo', $playbackInfo);
        }

    }

    //function to fetche more songs from the users library
    //this is called using ajax when the user scrolls to the bottom of the page
    //return: JSON response containing the new songs, or fail message
    //append them to the table
    public function fetchMoreSongs(Request $request){
        //before loading the host library, make sure the party setting allow it
        $timeStart = microtime(true);

        $userID;
        if(Auth::check()){ 

            $apiUser;
            if($request->session()->has('spotifyApiUserId')){
                $apiUser = session('spotifyApiUserId'); //will be user or host id
            }
            else{
                //go to the search page
                return redirect()->route('search.index');
            }
            
            //if all songs loaded, do nothing
            if(  $request->session()->has('allUserLibLoaded')){
                if(session('allUserLibLoaded') == 1){
                    return \Response::json("No New Songs");
                }
            }
            
            $spotifyInfo = $this->getApi($apiUser);

            $spotifyApi = $spotifyInfo[0];
            $spotifySession = $spotifyInfo[1];

            //load user tracks, using the number of songs loaded as the offset
            //refresh the tokens

            $newTracks = $spotifyApi->getMySavedTracks([
                'limit' => 50,
                'offset' => session('numOfSongsLoaded'),
            ]);

            //check if all songs loaded

            $numberOfSongsLoaded = count($newTracks->items);

            //get the total amount of songs that have been loaded, used for offset
            //in next function call

            session(['numOfSongsLoaded' => ($numberOfSongsLoaded + session('numOfSongsLoaded'))]);

            if($numberOfSongsLoaded < 50){
                //no more songs to be loaded
                session(['allUserLibLoaded' => 1]);
            }
            else{
                //more songs to load
                session(['allUserLibLoaded' => 0]);

            }

            //refresh tokens in db
            $this->refreshTokens($spotifyApi, $spotifySession, $apiUser);
            
            $timeEnd = microtime(true);

            $executionTime = $timeEnd - $timeStart;

            //dd($executionTime*1000);

            //return the new tracks to add in a jSON response
            return \Response::json($newTracks);
        }
    }

    public function fetchMoreTracksForPlaylist(Request $request){
        //check what api to use
        $apiUser;
        if($request->session()->has('spotifyApiUserId')){
            $apiUser = session('spotifyApiUserId'); //will be user or host id
        }
        else{
            //go to the search page
            return redirect()->route('search.index');
        }

        //if all songs on playlist are loaded, return
        if($request->session()->has('allPlaylistTracksLoaded')){
            if(session('allPlaylistTracksLoaded') === true){
                return \Response::json("all tracks loaded");
            }
        }

        $spotifyInfo = $this->getApi($apiUser);

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];


        $newPlaylistTracks = $spotifyApi->getPlaylistTracks($request->playlistid, [ 
            'limit' => 50,
            'offset' => session("numberOfTracksInPlaylist"),
        ]);

        if(count($newPlaylistTracks->items) < 50){
            session(['allPlaylistTracksLoaded' => true]);
        }
        else{
            session(["numberOfTracksInPlaylist" => count($newPlaylistTracks->items) + session("numberOfTracksInPlaylist")]);
        }

        $this->refreshTokens($spotifyApi, $spotifySession, $apiUser);

        return \Response::json($newPlaylistTracks);
    }

    //function to freshen up user access and reftresh tokens
    //returns an API to use to make calls
    public function getApi($userID){
        $userSession = new Session(
            '1d53c77dcd0e4cb6b6191f74e2ce6c51',
            '18e12cd385514d91a4d0aebb75e01423'
        );

        $user = User::find($userID);

        $accessToken = $user->spotifyUserAccessToken;
        $refreshToken = $user->spotifyUserRefreshToken;


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

    }

    public function refreshTokens($api, $session, $userID){
        //fetch the user to refresh
        $user = User::find($userID);

        $user->spotifyUserAccessToken = $session->getAccessToken();
        $user->spotifyUserRefreshToken = $session->getRefreshToken();

        $user->save();

    }

    public function queueSong(Request $request){
        //will be able to pass a param userid to queue songs to a certain user
        if(Auth::check()){ //if user is logged in
            //get the host id
            $hostid = Auth::user()->party->host_id;
            
            $songid = $request->input('songid');

            //dispatch the queue song job (wanted to incorperate a job)
            QueueSong::dispatch($songid, $hostid); 
            //only want to queue a song to the host
            //if the hsot is queueing a song it acts the same way since they are in the party
        }

        return \Response::json("success");

    }

    //process a spotify search request
    public function search(Request $request){
        //if the request does have a searchBar variable
        $searchResults;
        if($request->filled('search')){
            //perform the search on the request
            $searchQuery = $request->search;

            //fetch the api for the host
            //$user = User::find(Auth::id());

            $hostid = Auth::user()->party->host_id;

            $spotifyInfo = $this->getApi($hostid);
 
            $spotifyApi = $spotifyInfo[0];
            $spotifySession = $spotifyInfo[1];


            try {
                //search for the first 10 results for artist and track
                $searchResults = $spotifyApi->search($searchQuery, 'track,artist', [
                    'limit' => 10,
                ]);
            } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                dd("error:", $e);
            }

            //refresh the tokens after using them
            
            $this->refreshTokens($spotifyApi, $spotifySession, $hostid);

            //save the search query to a session variable
            session(['lastSearchQuery' => $searchQuery]);
            //save the results of the search into a session?
            session(['lastSearchResults' =>$searchResults]);
        }
        else{ //no search initaited, load search page with rpevioous search
            //if there is a previous search
            if($request->session()->has('lastSearchQuery')){
                $searchResults = session('lastSearchResults');
            }
            else{//else if there is no previous search
                //load the page with just the search bar
                $searchResults = null;
            }
        }

        $playbackInfo = $this->getCurrentlyPlayingTrack();        
        //return the userLib view and give the usersCurrentLib
        return view('spotifyRemoteControl/search')
            ->with('searchResults', $searchResults)
            ->with('playbackInfo', $playbackInfo);
    }

    //function to get current playback of party
    public function getCurrentlyPlayingTrack() {
        //check if a song is playing at all on the host topkens
        $hostid = Auth::user()->party->host_id;

        $spotifyInfo = $this->getApi($hostid);

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        $playbackInfo = $spotifyApi->getMyCurrentPlaybackInfo();

        //if there is a song playing
        if(isset($playbackInfo)){
            //check if the song is in the users library
            $songSaved; //bool

            if( isset(Auth::user()->spotifyUserAccessToken) ){
                //check if the song is saved in their library
                //only check if the song is not a local file
                if(!$playbackInfo->item->is_local){
                    $songSaved = $this->checkIfTrackSaved($playbackInfo->item->id);
                    session(['songSaved' => $songSaved[0]]); //bool
                }
            }

            $this->refreshTokens($spotifyApi, $spotifySession, $hostid);
            
            return $playbackInfo;
        }
        
        else{ //no song is playing
            $this->refreshTokens($spotifyApi, $spotifySession, $hostid);

            return NULL;
        }
    }

    //function called using AJAX
    //returns a json response to the ajax call

    public function refreshPlaybackInfo(){

        $hostid = Auth::user()->party->host_id;

        $spotifyInfo = $this->getApi($hostid);

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        $playbackInfo = $spotifyApi->getMyCurrentPlaybackInfo();

        //if there is a song playing
        if(isset($playbackInfo)){
            //check if the song is in the users library
            $songSaved = false; //bool

            //check if the user is connected to spotify
            if( isset(Auth::user()->spotifyUserAccessToken) ){
                //check if the song is saved in their library
                //only if the current song is not local
                if(!$playbackInfo->item->is_local){
                    $songSaved = $this->checkIfTrackSaved($playbackInfo->item->id);
                    session(['songSaved' => $songSaved[0]]); //bool
                }
            }

            $this->refreshTokens($spotifyApi, $spotifySession, $hostid);
            return \Response::json(array(
                'playbackInfo' => $playbackInfo,
                'songSaved' => $songSaved,
            ));
        }
        
        else{ //no song is playing
            $this->refreshTokens($spotifyApi, $spotifySession, $hostid);

            return \Response::json( "fail");
        }
    }

    //function returns a boolean
    public function checkIfTrackSaved($trackid){

        $spotifyInfo = $this->getApi(Auth::id());

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        $trackInLib = $spotifyApi->myTracksContains($trackid);

        $this->refreshTokens($spotifyApi, $spotifySession, Auth::id());

        return $trackInLib;
    }

    public function getArtist($artistid) {
        //use host api to search for the artist
        //$user = User::find(Auth::id());

        $hostid = Auth::user()->party->host_id;

        $spotifyInfo = $this->getApi($hostid);

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        $artistFetchResult = $spotifyApi->getArtist($artistid);

        //fetch artist top tracks
        $artistTopTracks = $spotifyApi->getArtistTopTracks($artistid, [
            'country' => 'us',
        ]);

        //fetch artist albums
        $artistAlbums = $spotifyApi->getArtistAlbums($artistid);

        //combine the three into an array
        $artistCombinedResults = array(
            'artistInfo' => $artistFetchResult,
            'artistTopTracks' => $artistTopTracks,
            'artistAlbums' => $artistAlbums

        );

        $playbackInfo = $this->getCurrentlyPlayingTrack();    
        
        $this->refreshTokens($spotifyApi, $spotifySession, $hostid);

        //return the artist view and pass the combined array

        return view('spotifyRemoteControl/search/artist')
            ->with('artist', $artistCombinedResults)
            ->with('playbackInfo', $playbackInfo);


    }

    public function getAlbum($albumid) {
        //$user = User::find(Auth::id());

        $hostid = Auth::user()->party->host_id;

        $spotifyInfo = $this->getApi($hostid);

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        $albumInfo = $spotifyApi->getAlbum($albumid);

        //refresh the player
        $playbackInfo = $this->getCurrentlyPlayingTrack();    

        //refresh the tokens
        $this->refreshTokens($spotifyApi, $spotifySession, $hostid);

        return view('spotifyRemoteControl/search/album')
            ->with('album', $albumInfo)
            ->with('playbackInfo', $playbackInfo);
    }

    public function getPlaylists(Request $request) {
        //get the user or hosts playlists
        //check what api to use
        $apiUser;
        //get the user id of the correct api to use form a session
        //'spotifyApiUserId = the user_id of the current api to use (host or current user)
        if($request->session()->has('spotifyApiUserId')){
            $apiUser = session('spotifyApiUserId'); //will be user or host id
        }
        //this session var is set when logging in, connecting spotyiy, or joingn party
        //if no session var is set, user cannot view any libraries or playlists
        else{
            //go to the search page
            return redirect()->route('search.index');
        }

        $spotifyInfo = $this->getApi($apiUser);

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        //the api set is going to be the user we need, so just use that
        $listOfPlaylists = $spotifyApi->getMyPlaylists();

        //return the list of playlists to the playlists view
        //the view will display a liost of the playlists, 
            //and ciewing a single playlist will load more infop about it

        $playbackInfo = $this->getCurrentlyPlayingTrack();    

        
        $this->refreshTokens($spotifyApi, $spotifySession, $apiUser);

        //dd($listOfPlaylists);

        return view('spotifyRemoteControl/playlists')
            ->with('listOfPlaylists', $listOfPlaylists)
            ->with('playbackInfo', $playbackInfo);
       
    }

    public function fetchPlaylist($playlistid){

        $hostid = Auth::user()->party->host_id;

        $spotifyInfo = $this->getApi($hostid);

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        //fetch the singular playlist
        $playlistInfo = $spotifyApi->getPlaylist($playlistid);

        session(["numberOfTracksInPlaylist" => count($playlistInfo->tracks->items)]);

        if(count($playlistInfo->tracks->items) < 50){
            session(['allPlaylistTracksLoaded' => true]);
        }
        else{
            session(['allPlaylistTracksLoaded' => false]);

        }

        $playbackInfo = $this->getCurrentlyPlayingTrack();    

        $this->refreshTokens($spotifyApi, $spotifySession, $hostid);

        return view('spotifyRemoteControl/playlist')
            ->with('playlist', $playlistInfo)
            ->with('playbackInfo', $playbackInfo);
    }
    
    public function deleteFromTracks(Request $request){
        //get song id form request
        $songid = $request->input('songid');

        //get the users spotify api
        $spotifyInfo = $this->getApi(Auth::id());

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        //call the spotify api to remove the song from the library
        $wasRemoved = $spotifyApi->deleteMyTracks($songid);

        //refresh the tokens
        $this->refreshTokens($spotifyApi, $spotifySession, Auth::id());

        //return json repsonse
        if($wasRemoved){
            return \Response::json("success");

        }
        else{
            return \Response::json("fail");

        }

    }

    public function addToTracks(Request $request){
        //dd("adding to tracks function in cointroller");
        $songid = $request->input('songid');

        if($songid == null){
            return \Response::json("song id is null");

        }

        

        $spotifyInfo = $this->getApi(Auth::id());

        $spotifyApi = $spotifyInfo[0];
        $spotifySession = $spotifyInfo[1];

        $wasAdded = $spotifyApi->addMyTracks($songid);

        //refresh the tokens
        $this->refreshTokens($spotifyApi, $spotifySession, Auth::id());

        //return json repsonse
        if($wasAdded){


        }

        return \Response::json("success");

        


    }

    //function to unlink a spotify account from the currentr user
    public function removeSpotifyAccount(){

        //check if user is hosting a party
        $hostedParty = Auth::user()->host;
        if(isset($hostedParty)){
            //remove party
            $hostedParty->delete();
        }
        //remove session variables
            //if user has loaded THEIR lib
            //any other flags

        //set the user model attributes to nbull
        $user = Auth::user();

        $user->spotifyUserAccessToken = null;
        $user->spotifyUserRefreshToken = null;

        $user->save();

        //return the user to the dashboard view
        return redirect()->route('party.index');

    }

    public function swapLib(){
        //swap the current lib for the other available lib
        //only alloy a swap if both user connected and host lib available
        
        //if the current lib is the user
        if(session('spotifyApiUserId') == Auth::id()){
            //swap to the host
            $hostid = Auth::user()->party->host_id;
            session(['spotifyApiUserId' => $hostid]);
        }
        else{ //swap to current user
            session(['spotifyApiUserId' => Auth::id()]);
        }

        //rerouta back to the previous view
        return redirect()->back();

    }
}
