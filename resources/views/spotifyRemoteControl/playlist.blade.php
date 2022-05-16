<x-spotify-layout>
    <div class=" bg-black text-white">
        <div>
            <!-- Playlist image -->
            <div>
                <img class=" mx-auto max-h-[300px] pt-2" src=" {{$playlist->images[0]->url }}" alt="">
                <p class="text-5xl"> {{ $playlist->name }}</p>
            </div>

            <table style="">
                @foreach ($playlist->tracks->items as $track)
                    <tr style="">
                        <td class=" w-1/5 p-2 pl-4">
                            <img src="{{$track->track->album->images[2]->url}}" alt="">
                        </td>

                        <td>
                            <p> {{$track->track->name }}</p>
                            <p> {{$track->track->artists[0]->name }}</p>

                        </td>
                         <!-- <td style="width: 25%; padding: 8px; padding-left: 16px"> -->
                        
                        
                    </tr>
                @endforeach
                <tr>

                </tr>
            </table>

        </div>
            

        <div class="footerSpacer">

        </div>

    </div>

    <!--     FOOTER  -->
    <x-slot name="footer">

        <x-footer :currentSong="$playbackInfo"/>


    </x-slot>

</x-app-layout>