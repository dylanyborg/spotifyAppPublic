<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PartyResourceController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('party.index');

})->middleware(['auth'])->name('party');

Route::get('/spotifyController', function () {
    return redirect()->route('userLibrary.show');

});

/*
grouping contorllers together for easier syntax
Route::controller(SpotifyController::class)->group(function () {
    Route::get('/spotifyAuth', 'login')->name('spotifyAuth');
})
*/
Route::controller(SpotifyController::class)->group(function() {
    Route::get('/spotifyAuth', 'login')->name('spotifyAuth');
    Route::get('/callback', 'callback');

    Route::get('spotifyController/userLibrary', 'loadUserLibrary')
        ->middleware(['auth', 'spotifyParty', 'spotifyLibAccess'])
        ->name('userLibrary.show');
    
    Route::post('/removeSpotifyAccount', 'removeSpotifyAccount')
        ->name('spotifyAccount.delete');

    Route::post('/spotifyController/userLibrary/queue', 'queueSong')
        ->name('queueTrack');

    Route::get('/spotifyController/search', 'search')
        ->middleware(['spotifyParty'])->name('search.index');

    Route::get('/spotifyController/artist/{artistid}', 'getArtist')
        ->name('artist.show');
    Route::get('/spotifyController/album/{albumid}', 'getAlbum')
        ->name('album.show');

    Route::get('/spotifyController/playlists', 'getPlaylists')
        ->name('playlists.index');
    Route::get('/spotifyController/playlist/{playlistid}',  'fetchPlaylist')
        ->name('playlist.show');

    Route::post('/spotifyController/deleteFromLib', 'deleteFromTracks');
    Route::post('/spotifyController/userLibrary/addToLib', 'addToTracks')
        ->name('addToLib');

    Route::post('/spotifyController/refreshPlaybackInfo', 'refreshPlaybackInfo');

    Route::get('/spotifyController/swapLibrary', 'swapLib')
        ->name('swapLib');

});

//function for authorizing spotify account
//Route::get('/spotifyAuth', [SpotifyController::class, 'login'])->name('spotifyAuth');

//Route::get('/callback', [SpotifyController::class, 'callback']);

//remove spotify account
//Route::post('/removeSpotifyAccount', [SpotifyController::class, 'removeSpotifyAccount'])
//->name('spotifyAccount.delete');

//spotify web api call functions
//Route::get('/spotifyController/userLibrary', [SpotifyController::class, 'loadUserLibrary'])
//->middleware(['auth', 'spotifyParty', 'spotifyLibAccess'])->name('userLibrary.show');

//Route::post('/spotifyController/userLibrary/queue', [SpotifyController::class, 'queueSong'])
//->name('queueTrack');

/*
search spotify routes

*/
//Route::get('/spotifyController/search', [SpotifyController::class, 'search'])
//->middleware(['spotifyParty'])->name('search.index');

//get artist
//Route::get('/spotifyController/artist/{artistid}', [SpotifyController::class, 'getArtist'])
//->name('artist.show');

//Route::get('/spotifyController/album/{albumid}', [SpotifyController::class, 'getAlbum'])
//->name('album.show');

//playlists
//Route::get('/spotifyController/playlists', [SpotifyController::class, 'getPlaylists'])
//->name('playlists.index');

//Route::get('/spotifyController/playlist/{playlistid}', [SpotifyController::class, 'fetchPlaylist'])
//->name('playlist.show');


//Route::post('/spotifyController/deleteFromLib', [SpotifyController::class, 'deleteFromTracks']);

//Route::post('/spotifyController/userLibrary/addToLib', [SpotifyController::class, 'addToTracks']);

//Route::post('/spotifyController/userLibrary/addToLib', [SpotifyController::class, 'addToTracks'])
//->name('addToLib');

//Route::post('/spotifyController/refreshPlaybackInfo', [SpotifyController::class, 'refreshPlaybackInfo']);
/*
    Party routes
*/

//set the party join named route before the resource base routes
Route::post('/dashboard/party/join', [PartyController::class, 'join'])
->name('party.join');

Route::post('/dashboard/party/leave', [PartyController::class, 'leave'])
->name('party.leave');

//use middleware to make sure the user is a host
Route::post('/dashboard/party/lock', [PartyController::class, 'lock'])
->name('party.lock');

//party resource controller
Route::resource('/dashboard/party', PartyResourceController::class);

//create a party
//Route::resource('dashboardparty/')




require __DIR__.'/auth.php';
