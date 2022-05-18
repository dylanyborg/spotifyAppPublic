<x-spotify-layout>
    

    <!-- modal popup -->
    <div id="queueConfirmModal" class ="modal">

        <div class="modal-content">
            <h5 class=" text-center"> Song added to queue </h5>
        </div>
    </div>

    <x-song-table :currentTracks="$tracks">
        <x-slot name="title">
            <!-- Display the name of the playlist of songs (liked songs, a playlist, etc)
                Add a dropdown to change from current users to hosts library
            for now its just usrs liobrary -->
            @if(session()->has('spotifyApiUserId'))

                @if (session('spotifyApiUserId') === Auth::id())
                <!-- Users library is beinbg shown -->
                    {{ Auth::user()->username }}'s library
                    @if (!Auth::user()->party->hideHostLibrary)
                        <x-slot name="lib2">
                            Host: {{ Auth::user()->party->host->username}}'s library
                        </x-slot>
                    @endif
                    
                @else
                <!-- Host library is being shown. Get name of party host -->
                    Host: {{ Auth::user()->party->host->username}}'s library
                    
                    @isset(Auth::user()->spotifyUserAccessToken)
                        <x-slot name="lib2">
                            {{ Auth::user()->username }}'s library
                        </x-slot>
                    @endisset
                    
                @endif
            @endif
            

        </x-slot>

        <table>
            <tbody id="tableSongs">
            @foreach ($tracks->items as $tracks)
                <x-song-table-row>
                    <x-slot name="albumArt">
                        {{ $tracks->track->album->images[2]->url }}
                    </x-slot>

                    <x-slot name="trackid">
                        {{$tracks->track->id}}
                    </x-slot>
                    <x-slot name="trackName">
                        {{ $tracks->track->name }}
                    </x-slot>

                    <x-slot name="artistName">
                        {{ $tracks->track->artists[0]->name }}
                    </x-slot>
                    <x-slot name="artistid">
                        {{ $tracks->track->artists[0]->id }}
                    </x-slot>

                    <x-slot name="albumName">
                        {{ $tracks->track->album->name}}
                    </x-slot>
                    <x-slot name="albumid">
                        {{ $tracks->track->album->id}}
                    </x-slot>

                    <x-slot name="queueButton">
                        Add to queue
                    </x-slot>

                    <x-slot name="albumLink">
                        View album
                    </x-slot>

                    <x-slot name="artistLink">
                        View Artist
                    </x-slot>

                   
                    
                </x-song-table-row>
            @endforeach
            </tbody>

            
        </table>

        


    </x-song-table>

    <div class="footerSpacer">

    </div>

    <script type="text/javascript">
        $(window).scroll(function() {
            if( $(window).height() + $(window).scrollTop() == $(document).height() ){
                console.log("scrolled to bottom");
                fetchMoreTracks();
            }
        });


    </script>

    <!--     FOOTER  -->
    <x-slot name="footer">

        <x-footer :currentSong="$playbackInfo"/>


    </x-slot>
</x-spotify-layout>