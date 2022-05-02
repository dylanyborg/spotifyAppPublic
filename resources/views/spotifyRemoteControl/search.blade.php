<x-spotify-layout>
    
    <div>
        <div class="w-full">
            <h1> Search: </h1>

            <form method="GET" action="{{ route('search.index') }}">
    
                <input data="{{ session('lastSearchQuery') }}" class="w-full" type="search" name="search" id="searchBar" placeholder="Song, Artist" aria-label="Search"/>
                
            </form>
        </div>
        

        @isset($searchResults)
            <div class="searchResults">
                <div class="listOfSongs">
                    <h1>
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
                            </tr>
                        
                        @endforeach
                    </table>
                </div>

                <div class="searchResultArtist">
                    <h1>
                        Artists:
                    </h1>
                    <table>
                        @foreach ($searchResults->artists->items as $artist)
                        <tr>
                            <td>
                                @isset($artist->images[2]->url)
                                    <img src="{{$artist->images[2]->url}}" alt="{{$artist->name}}" >
                                @endisset
                            </td>
                            <td>
                                {{ $artist->name }}
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

</x-app-layout>