@if (isset($currentSong))

    <div class="currentlyPlaying">
        <div class="songInfo">
            <div class="smallAlbumArt">
                <img src="{{ asset('images/Spotify_Icon_RGB_Green.png')}}">
            </div>

            <div class="songName">
                <p id="songName" class="truncate text-base"> {{ $currentSong->item->name }} </p> 
                <p id="songArtist" class="truncate text-sm"> {{$currentSong->item->artists[0]->name}} &#8226 {{$currentSong->item->album->name}}  </p>
            </div>

            <!-- liked song heart. Only show if the user has linked a personal account -->
            @if ( isset(Auth::user()->spotifyUserAccessToken) )
                <div id="songSaved" style="width: 13%; margin-top:auto; margin-bottom:auto">

                    
                        
                        @if (session("songSaved") == true)
                            <button type="submit" value="delete" id="likedSongButton" data-id="{{ $currentSong->item->id}}">
                                <img id="likedSongImage" src="{{ asset('images/spotifyHeartLiked.svg')}}" alt="&#x2764">
                            </button>
                        @else 
                            <button type="submit" value="add" id="likedSongButton" data-id="{{ $currentSong->item->id}}">
                                <img id="likedSongImage"  src="{{ asset('images/spotifyHeartUnliked.svg')}}" alt="&#x2764">
                            </button>
                        @endif
                    
                    
                </div>
                
            @endif
            
        </div>
        <div class="progressBarOutline" >
            <div class="progressBar" id="progressBar">
                <input type="hidden" name="progress_ms" id="progress_ms" value="{{ $currentSong->progress_ms }}">
                <input type="hidden" name="duration_ms" id="duration_ms" value="{{ $currentSong->item->duration_ms }}">
            </div>
        </div>

    </div>
@endif



<div class="spotifyNavBar">
    <nav>
        <div class="spotifyControllerHeader">
            <x-nav-link class=" text-white" :href="route('userLibrary.show')" :active="request()->routeIs('userLibrary.show')"  >
                {{ __('Liked Songs')}}
            </x-nav-link>

            <x-nav-link class=" text-white" :href="route('search.index')" :active="request()->routeIs('search.index')"  >
                {{ __('Search')}}
            </x-nav-link>
            
            <x-nav-link class=" text-white" :href="route('playlists.index')" :active="request()->routeIs('playlists.index')" >
                {{ __('Playlists')}}
            </x-nav-link>
        </div>
    </nav>

</div>
