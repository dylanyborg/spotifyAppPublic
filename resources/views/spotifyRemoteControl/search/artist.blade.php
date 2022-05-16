<x-spotify-layout>
    <div class=" bg-black text-white">
            <!-- artist image large and name  -->
        <div>
            <img class=" mx-auto pt-2" style="max-height: 300px" src="{{ $artist['artistInfo']->images[0]->url}}" alt="{{ $artist['artistInfo']->name}}">
            <p class="text-5xl" >  {{ $artist['artistInfo']->name}} </p>
            
        </div>

        <!-- Popular songs -->
        <div class=" mt-4">
            <h1 class="text-4xl"> Popular</h1>

            <x-song-table>
                <x-slot name="title">

                </x-slot>

                <table>
                    @foreach ($artist['artistTopTracks']->tracks as $track)
                        <tr>
                            <td  class=" text-center">
                                 {{ $loop->iteration }}
                            </td>
                            <td class=" w-20">
                                <img src="{{ $track->album->images[2]->url }}" alt="album image">
                            </td>
                            <td>
                                <p>
                                    {{ $track->name }}
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
        
                                        </div>
        
        
                                    </x-slot>
        
                                </x-dropdown>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </x-song-table>
            
            

        </div>

        <div class="mt-4">
            <h1 class="text-3xl"> Albums</h1>
            <div class="flex flex-col">
                @foreach ($artist['artistAlbums']->items as $album)
                    <div class="mt-2 flex ">
                        <div class="flex-shrink-0">
                            <img width="84px " height="84px" src="{{ $album->images[1]->url }}" alt="">
                        </div>

                        <div class=" flex-shrink" style="padding: 20px 0 20px 10px">
                            <a href="{{route('album.show', $album->id) }}">
                                <p> {{ $album->name }}  </p>
                                <p> {{ $album->release_date }}</p>
                            </a>
                        </div>

                    </div>
                @endforeach
                

            </div>
            
        </div>



        <!-- Albums -->
        <div class=" mt-4">
            <h1 class="text-3xl"> Albums</h1>

            

            <table class=" w-full border-separate ">
                @foreach ($artist['artistAlbums']->items as $album)
                    <tr style="margin-bottom: 12px">
                        <td class=" w-20">
                            <img  src="{{ $album->images[1]->url }}" alt="">
                        </td>
                        <td style="padding-left: 8px">
                            <p> {{ $album->name }}</p>
                            <p> {{ $album->release_date }}</p>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

    </div>

    <div class="footerSpacer">

    </div>

    <!--     FOOTER  -->
    <x-slot name="footer">

        <x-footer :currentSong="$playbackInfo"/>


    </x-slot>
</x-spotify-layout>