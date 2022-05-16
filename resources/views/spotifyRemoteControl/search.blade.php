<x-spotify-layout>
<div style="width: 95%; margin-right:auto; margin-left:auto">

    <!-- modal popup -->
    <div id="queueConfirmModal" class ="modal">

        <div class="modal-content">
            <h5 class=" text-center"> Song added to queue </h5>
        </div>
    </div>
    
    <div>
        <div class="w-full" style="color: white; position:sticky; position:-webkit-sticky">
            <h1 style="font-size: 30px"> <b> Search: </b> </h1>

            <form method="GET" action="{{ route('search.index') }}">
                <div>
                    <input style="border-radius: 10px; color:black; width:100%" placeholder="{{ session('lastSearchQuery') }}" class="w-full" type="search" name="search" id="searchBar"  aria-label="Search"/>

                </div>
                
            </form>
        </div>
        

        @isset($searchResults)
            <div class="searchResults" style="color: white">
                <div class="listOfSongs">
                    <h1 style="font-size: 25px">
                        Songs:
                    </h1>
                    <table style="width: 100%">

                        @foreach ($searchResults->tracks->items as $track)
                            <tr>
                                <td class="albumArt">
                                    <img src="{{ $track->album->images[2]->url}}" alt="{{$track->album->name}}">
                                </td>
                                <td>
                                    <p> {{$track->name}}</p>
                                    <p style="font-size: 12px"> 
                                        @foreach($track->artists as $artist)
                                            @if ($loop->last)
                                                {{ $artist->name}}
                                            @else
                                                {{ $artist->name}}, 
                                            @endif
                                            
                                        @endforeach
                                    </p>
                                </td>
                                <td>
                                    <x-dropdown align="right" width="32">
                                        <x-slot name="trigger">
                                            <button class=" w-10">
                                                <p style="font-size: 20px"> ... </p>
                                            </button>
                                        </x-slot>
            
                                        <x-slot name="content">
                                            <div class=" text-center mx-1" style="background-color:#191414 ">
                                                <!-- Add to queue -->
                                                <button type="button" name="queueButton" data-id="{{$track->id}}">
                                                    <p>
                                                        queue song
                                                    </p>
                                                </button>
            
                                                <div>
                                                    <a href="{{ route('album.show', $track->album->id) }} ">
                                                        <p>
                                                            go to album
                                                        </p>
                                                    </a>
                                                </div>
            
                                                <div>
                                                    <a href="{{ route('artist.show', $track->artists[0]->id) }}">
                                                        go to artist
                                                    </a>
                                                </div>
                                            </div>
            
            
                                        </x-slot>
            
                                    </x-dropdown>
                                </td>
                            </tr>
                        
                        @endforeach
                    </table>
                </div>

                <div class="searchResultArtist">
                    <h1 style="font-size: 25px">
                        Artists:
                    </h1>
                    <table class=" w-full border-separate">
                        @foreach ($searchResults->artists->items as $artist)
                        <tr>
                            <td class=" w-20" style="padding: 10px">
                                @isset($artist->images[1]->url)
                                    <img src="{{$artist->images[1]->url}}" alt="{{$artist->name}}" >
                                @else
                                    <img src="{{ asset('images/Spotify_Icon_RGB_Green.png')}}">
                                @endisset
                            </td>
                            <td style="padding-left: 10px">
                                <a href="{{route('artist.show', $artist->id)}}">
                                    {{ $artist->name }}
                                </a>
                                
                            </td>
                        </tr>
                            
                        @endforeach
                    </table>

                </div>
                
            </div>
        @endisset
        
    </div>

    <div class="footerSpacer">

    </div>

    <!--     FOOTER  -->
    <x-slot name="footer">

        <x-footer :currentSong="$playbackInfo"/>


    </x-slot>

</div>


</x-app-layout>