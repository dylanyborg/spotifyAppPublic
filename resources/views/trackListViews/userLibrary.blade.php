<x-app-layout>
    <!--     HEADER -->
    <x-slot name="header">
        <h2>
            
            Spotify Library
        </h2>
    </x-slot>

    <!--  BODY   -->
    <!-- Use a component to display each song on the view --> 
    <div>
        Songs go here 
        
        

        <table>
            @foreach ($tracks as $tracks)
                <tr>
                    <td>
                        <button name="queueButton" class="queueButton" 
                            data-id={{ $tracks->track->id }}>

                            Queue

                        </button>
                    </td>
                    <td>
                        {{ $tracks->track->name }}
                    </td>
                    <td>
                        {{ $tracks->track->artists[0]->name }}
                    </td>
                </tr>
            @endforeach
        </table>

        <!-- AJAX script -->
        <script src="{{ asset('js/ajax-post.js') }}" defer></script>
        
      



        

        
        
    </div>

</x-app-layout>