<div class="currentlyPlaying">
    @if (isset($currentSong))
        <div class="songInfo">
            <div class="smallAlbumArt">
                <img src="{{ asset('images/Spotify_Icon_RGB_Green.png')}}">
            </div>

            <div class="songName">
                <p class="truncate text-base"> {{ $currentSong->item->name }} </p> 
                <p class="truncate text-sm"> {{$currentSong->item->artists[0]->name}} &#8226 {{$currentSong->item->album->name}}  </p>
            </div>

            <div>
                <p> &#x2764 </p>
            </div>

            <div>
                @if ($currentSong->is_playing)
                    Playing
                @elseif (!$currentSong->is_playing)
                    Paused
                @endif
            </div>
            
            
        </div>
        <div class="progressBarOutline">
            <div class="progressBar">

            </div>
        </div>
    @endif
</div>

<div class="spotifyNavBar">
    <nav>
        <div class="spotifyControllerHeader">
            <x-nav-link :href="route('userLibrary.show')" :active="request()->routeIs('userLibrary.show')"  >
                {{ __('Liked Songs')}}
            </x-nav-link>

            <x-nav-link :href="route('search.index')" :active="request()->routeIs('search.index')"  >
                {{ __('Search')}}
            </x-nav-link>
            
            <x-nav-link>
                {{ __('Playlists')}}
            </x-nav-link>
        </div>
    </nav>

</div>
