<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SpotifyController;

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
})->middleware(['auth'])->name('dashboard');

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
    ->name('userLibrary.show');

Route::get('/spotifyController/userLibrary/queue/{trackid}', [SpotifyController::class, 'queueSong'])
->name('queueTrack');

require __DIR__.'/auth.php';
