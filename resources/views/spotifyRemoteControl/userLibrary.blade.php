<x-app-layout>
    <!--     HEADER -->
    <x-slot name="header">
        <h2>
            <a href=" {{ route('userLibrary.show') }}">
                Spotify Library
            </a>
            <a href=" {{ route('search.index') }}">
                search
            </a>
        </h2>
    </x-slot>

    <!--  BODY   -->
    <!-- Use a component to display each song on the view --> 
    <div class='listOfSongs'>
        Songs go here 
        
        

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
        
      



        

        
        
    </div>

</x-app-layout>