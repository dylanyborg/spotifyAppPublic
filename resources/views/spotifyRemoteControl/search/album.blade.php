<x-spotify-layout>
    <div class=" bg-black text-white">
            <!-- Album image large and name  -->
        <div>
            <img class=" mx-auto pt-2" style="max-height: 300px" src="{{ $album->images[0]->url}}" alt="{{ $album->name}}">
            <p class="text-5xl" >  {{ $album->name}} </p>
            
        </div>

            <!-- modal popup -->
        <div id="queueConfirmModal" class ="modal">

            <div class="modal-content">
                <h5 class=" text-center" style="color: black"> Song added to queue </h5>
            </div>
        </div>

        <!-- album tracks -->
        <div>
            <x-song-table>
                <x-slot name="title">

                </x-slot>

                <table>
                    @foreach ($album->tracks->items as $track)
                        <tr>
                            <td>
                                {{$track->name}}
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
                                            <button type="button" name="queueButton" data-id="{{$track->id}}">
                                                <p>
                                                    queue song
                                                </p>
                                            </button>
        
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
                <script src="{{ asset('js/ajax-post.js') }}" defer></script>
            </x-song-table>
        </div>


    



        

    </div>

    <div class="footerSpacer">

    </div>

    <!--     FOOTER  -->
    <x-slot name="footer">

        <x-footer :currentSong="$playbackInfo"/>


    </x-slot>
</x-spotify-layout>