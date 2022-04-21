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
        <form method="GET" action="{{ route('queueTrack') }}">
            @csrf

        
            <table>
                @foreach ($tracks->track as $track)
                    
                    <tr>
                        <td style="padding: 5px"> 
                            <x-queue-button>
                                Queue  
                            </x-queue-button>
                            
                        </td>
                        <td> {{ $track->name }} </td>
                        <td> {{ $track->artists[0]->name}} </td>
                    </tr>
                @endforeach
            </table>

        </form>
        
    </div>

</x-app-layout>