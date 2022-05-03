<x-spotify-layout>
    <div class=" bg-black text-white">
            <!-- Album image large and name  -->
        <div>
            <img class=" mx-auto pt-2" style="max-height: 300px" src="{{ $album->images[0]->url}}" alt="{{ $album->name}}">
            <p class="text-5xl" >  {{ $album->name}} </p>
            
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
                                ... 
                            </td>
                        </tr>
                        
                    @endforeach
                </table>
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