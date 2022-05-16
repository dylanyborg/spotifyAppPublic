<x-spotify-layout>
    <div class=" text-white" style="background-color: #191414">
        <div>
            <p style="font-size: 30px">
                Playlists:
            </p>

            <table style="">
                @foreach ($listOfPlaylists->items as $playlist)
                    <tr style="">
                         <!-- <td style="width: 25%; padding: 8px; padding-left: 16px"> -->
                        <td class="w-1/4 p-2 pl-4">
                            <img src="{{ $playlist->images[0]->url }}" alt="image">
                        </td>
                        <td style="padding-left: 8px; padding-right: 8px;">
                            <a href="{{ route('playlist.show', $playlist->id) }}">
                                <p class=" text-base"> {{ $playlist->name }}</p>
                                <p class=" text-sm"> {{ $playlist->owner->display_name }} </p>
                            </a>
                        </td>
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