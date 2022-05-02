<x-spotify-layout>
    

    <!-- modal popup -->
    <div id="queueConfirmModal" class ="modal">

        <div class="modal-content">
            <p> Song Queued! </p>
        </div>
    </div>

    <x-song-table>
        <x-slot name="title">
            <!-- Display the name of the playlist of songs (liked songs, a playlist, etc)
                Add a dropdown to change from current users to hosts library
            for now its just usrs liobrary -->
            {{ Auth::user()->username }}'s library

        </x-slot>

        <table>
            @foreach ($tracks as $tracks)
                <tr>
                    <td>
                        <x-queue-button :songid="$tracks->track->id">
                            <p>
                                Queue
                            </p>

                        </x-queue-button>
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
                    
                </tr>
            @endforeach
        </table>

        

        <!-- AJAX script -->
        <script src="{{ asset('js/ajax-post.js') }}" defer></script>

    </x-song-table>

    <div class="footerSpacer">

    </div>

    <!--     FOOTER  -->
    <x-slot name="footer">

        <x-footer :currentSong="$playbackInfo"/>


    </x-slot>
</x-spotify-layout>