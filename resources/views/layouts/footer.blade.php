<div class="footer">
    <div class ="currentlyPlaying">
        <div class="songInfo">
            Song info here
        </div>
        <div class="trackProgressBar">
            small progress bar
        </div>

    </div>
    <div class="spotifyNavBar">
        <nav>
            <div class="spotifyControllerHeader">
                <x-nav-link :href="route('userLibrary.show')" :active="request()->routeIs('userLibrary.show')"  >
                    {{ __('Liked Songs')}}
                </x-nav-link>
                
                <a href=" {{ route('search.index') }}">
                    Search
                </a>
                <a href="#">
                    Playlists
                </a>
            </div>
        </nav>

    </div>
</div>