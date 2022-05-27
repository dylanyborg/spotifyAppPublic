<x-spotify-layout>

    <input type="hidden" name="playlistid" id="playlistid" value=" {{$playlist->id}}" >
    <div class=" bg-black text-white">
        <div>
            <!-- Playlist image -->
            <div>
                <img class=" mx-auto max-h-[300px] pt-2" src=" {{$playlist->images[0]->url }}" alt="">
                <p class="text-5xl"> {{ $playlist->name }}</p>
                <p> Created by: {{ $playlist->owner->display_name}}</p>
            </div>
            <div class="listOfSongs">

                
                <table style="">
                    <tbody id="tableSongs">
                        @foreach ($playlist->tracks->items as $tracks)
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
            </div>

        </div>
            

        <div class="footerSpacer">

        </div>

    </div>

    <script type="text/javascript">
        $(window).scroll(function() {
            if( $(window).height() + $(window).scrollTop() == $(document).height() ){
                console.log("scrolled to bottom");
                fetchMoreTracksForPlaylist();
            }
        });


    </script>

    <!--     FOOTER  -->
    <x-slot name="footer">

        <x-footer :currentSong="$playbackInfo"/>


    </x-slot>

</x-app-layout>