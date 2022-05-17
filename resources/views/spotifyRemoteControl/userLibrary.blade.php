<x-spotify-layout>
    

    <!-- modal popup -->
    <div id="queueConfirmModal" class ="modal">

        <div class="modal-content">
            <h5 class=" text-center"> Song added to queue </h5>
        </div>
    </div>

    <x-song-table>
        <x-slot name="title">
            <!-- Display the name of the playlist of songs (liked songs, a playlist, etc)
                Add a dropdown to change from current users to hosts library
            for now its just usrs liobrary -->
            @if(session()->has('spotifyApiUserId'))

                @if (session('spotifyApiUserId') === Auth::id())
                <!-- Users library is beinbg shown -->
                    {{ Auth::user()->username }}'s library
                    <x-slot name="lib2">
                        Host: {{ Auth::user()->party->host->username}}'s library
                    </x-slot>
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
            
            @foreach ($tracks as $tracks)
                <tr>
                    <td class=" w-1/5 p-2 pl-4">
                        <img src="{{ $tracks->track->album->images[2]->url }}" alt="">
                    </td>
                    <td class ='wideCell'>
                        <p>
                            {{ $tracks->track->name }}
                        </p>
                        <p style="font-size: 12px">
                            {{ $tracks->track->artists[0]->name }} &#8226
                            {{ $tracks->track->album->name}}
                        </p>
                    </td>

                    <td>
                        <x-dropdown align="right" width="32">
                            <x-slot name="trigger">
                                <button class=" bg-orange-600 w-10">
                                    <p > ... </p>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class=" bg-slate-800 text-center mx-1">
                                    <!-- Add to queue -->
                                    <button type="button" name="queueButton" data-id="{{$tracks->track->id}}">
                                        <p>
                                            queue song
                                        </p>
                                    </button>

                                    <div>
                                        <a href="{{ route('album.show', $tracks->track->album->id) }} ">
                                            <p>
                                                go to album
                                            </p>
                                        </a>
                                    </div>

                                    <div>
                                        go to artist
                                    </div>
                                </div>


                            </x-slot>

                        </x-dropdown>


                        
                    </td>
                    
                </tr>
            @endforeach

            
        </table>

        

        <!-- AJAX script -->

    </x-song-table>

    <div class="footerSpacer">

    </div>

    <!--     FOOTER  -->
    <x-slot name="footer">

        <x-footer :currentSong="$playbackInfo"/>


    </x-slot>
</x-spotify-layout>