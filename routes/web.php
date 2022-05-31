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
Route::middleware('auth')->group(function(){
    Route::controller(SpotifyController::class)->group(function() {
        Route::get('/spotifyAuth', 'login')->name('spotifyAuth');
        Route::get('/callback', 'callback');

        Route::get('spotifyController/userLibrary', 'loadUserLibrary')
            ->middleware(['spotifyParty', 'spotifyLibAccess'])
            ->name('userLibrary.show');
        
        Route::post('/removeSpotifyAccount', 'removeSpotifyAccount')
            ->name('spotifyAccount.delete');

        Route::post('/spotifyController/userLibrary/queue', 'queueSong')
            ->name('queueTrack');
        Route::post('/spotifyController/userLibrary/fetchMoreSongs', 'fetchMoreSongs')
            ->name('fetchMoreSongs');

        Route::get('/spotifyController/search', 'search')
            ->middleware(['spotifyParty'])->name('search.index');

        Route::get('/spotifyController/artist/{artistid}', 'getArtist')
            ->middleware(['spotifyParty'])
            ->name('artist.show');
        Route::get('/spotifyController/album/{albumid}', 'getAlbum')
            ->middleware(['spotifyParty'])
            ->name('album.show');

        Route::get('/spotifyController/playlists', 'getPlaylists')
            ->middleware(['spotifyParty', 'spotifyLibAccess'])
            ->name('playlists.index');
        Route::get('/spotifyController/playlist/{playlistid}',  'fetchPlaylist')
            ->middleware(['spotifyParty', 'spotifyLibAccess'])
            ->name('playlist.show');
        Route::post('/spotifyController/fetchMoreSongsForPlaylist',  'fetchMoreTracksForPlaylist');

        Route::post('/spotifyController/deleteFromLib', 'deleteFromTracks');
        Route::post('/spotifyController/userLibrary/addToLib', 'addToTracks')
            ->name('addToLib');

        Route::post('/spotifyController/refreshPlaybackInfo', 'refreshPlaybackInfo');

        Route::get('/spotifyController/swapLibrary', 'swapLib')
            ->middleware(['spotifyParty', 'spotifyLibAccess'])
            ->name('swapLib');

    });

    Route::controller(PartyController::class)->group(function() {
        //set the party join named route before the resource base routes
        Route::post('/dashboard/party/join', 'join')
        ->name('party.join');

        Route::post('/dashboard/party/leave',  'leave')
        ->name('party.leave');

        //use middleware to make sure the user is a host
        Route::post('/dashboard/party/lock', 'lock')
        ->name('party.lock');

        //party resource controller
    });

    Route::resource('/dashboard/party', PartyResourceController::class);

});








//create a party
//Route::resource('dashboardparty/')




require __DIR__.'/auth.php';
