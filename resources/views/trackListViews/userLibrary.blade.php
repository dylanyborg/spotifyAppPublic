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
                            <a href=" {{ route('queueTrack', ['trackid' => $tracks->track->id]) }}">
                                Queue
                            </a>
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

        
        

      



        

        
        
    </div>

</x-app-layout>