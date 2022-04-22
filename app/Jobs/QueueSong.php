<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

//spotify webapi files
require '../vendor/autoload.php';

use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class QueueSong implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $trackid, $userid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($trackid, $userid)
    {
        //
        $this->trackid = $trackid;
        $this->userid = $userid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //queue a song using spotoify webapi

        //get the right api to use
        $userSession = new Session(
            '1d53c77dcd0e4cb6b6191f74e2ce6c51',
            '18e12cd385514d91a4d0aebb75e01423'
        );

        //get users tokens to use spotify web api
        $user = User::find($this->userid);

        $accessToken = $user->spotifyUserAccessToken;
        $refreshToken = $user->spotifyUserRefreshToken;

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

        //using the api, queue the song
        $wasQueued = $userApi->queue($this->trackid);

        //get refreshed tokens
        $user->spotifyUserAccessToken = $userSession->getAccessToken();
        $user->spotifyUserRefreshToken = $userSession->getRefreshToken();

        $user->save();

        //dd($this->trackid);
    }
}
