<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\PartyController;


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
    return view('dashboard');
})->middleware(['auth'])->name('party');

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
->middleware(['auth'])->name('userLibrary.show');

Route::post('/spotifyController/userLibrary/queue', [SpotifyController::class, 'queueSong'])
->name('queueTrack');

/*
search spotify routes

*/
Route::get('/spotifyController/search', [SpotifyController::class, 'search'])
->name('search.index');

/*
    Dashboard routes
*/

//party

//index the party
Route::resource('/dashboard/party', PartyController::class);

//create a party
//Route::resource('dashboardparty/')




require __DIR__.'/auth.php';
