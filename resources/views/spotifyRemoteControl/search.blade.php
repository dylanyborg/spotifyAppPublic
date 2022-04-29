<x-app-layout>
    <!--     HEADER -->
    <x-slot name="header" >

        <h2>
            <a href=" {{ route('userLibrary.show') }}">
                Spotify Library
            </a>
            <a href=" {{ route('search.index') }}">
                search
            </a>
        </h2>
    </x-slot>

    <div>
        <h1> Search: </h1>

        <form method="GET" action="{{ route('search.index') }}">

            <input type="search" name="search" id="searchBar" placeholder="Song, Artist" aria-label="Search"/>
            
        </form>

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
                
            </div>
        @endisset
        
    </div>

</x-app-layout>