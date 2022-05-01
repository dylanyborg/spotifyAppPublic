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

//function for authorizing spotify account
Route::get('/spotifyAuth', [SpotifyController::class, 'login'])->name('spotifyAuth');

Route::get('/callback', [SpotifyController::class, 'callback']);

//spotify web api call functions
Route::get('/spotifyController/userLibrary', [SpotifyController::class, 'loadUserLibrary'])
->middleware(['auth', 'spotifyParty', 'spotifyLibAccess'])->name('userLibrary.show');

Route::post('/spotifyController/userLibrary/queue', [SpotifyController::class, 'queueSong'])
->name('queueTrack');

/*
search spotify routes

*/
Route::get('/spotifyController/search', [SpotifyController::class, 'search'])
->middleware(['spotifyParty'])->name('search.index');

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
