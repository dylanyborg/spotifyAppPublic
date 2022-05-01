<x-app-layout>
    <x-slot name="header">
        <h2>
            <div class="spotifyControllerHeader">
                
                <a href=" {{ route('userLibrary.show') }}">
                    Friends
                </a>
                <a href=" {{ route('search.index') }}">
                    Party
                </a>
            </div>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-lg"> Current Party </h1>

                    @if ($party->host_id == Auth::id())
                        <a href="{{ route('party.edit', $party->id) }}">
                            Edit Party
                        </a>

                        <div>
                            <form action="{{ route('party.lock', $party->id) }}" method="post">
                                @csrf

                                @if ($party->isLocked)
                                    <x-button name="lockButton" value="{{$party->id}}">
                                        {{ __('Unlock Party')}}
                                    </x-button>                                                               
                                @else
                                    <x-button name="lockButton" value="{{$party->id}}">
                                        {{ __('Lock Party')}}
                                    </x-button>
                                @endif

                            </form>
                            
                        </div>
                       
                    @else 
                        
                        @if ($party->isLocked)
                            <div>
                                The party is locked
                            </div>
                        @endif
                    @endif 

                    <!-- If the user is the host, display one of two buttons -->
                    <!-- else if not the host, display an iomage only if the party is locked -->
                    

                    

                    <p> Party Name: {{ $party->partyName }} </p>

                    <p> Party Host: {{ $party->host->username }} </p>

                    

                    
                    

                    <!-- Display party list -->
                    <h2> Users in Party:</h2>
                    <form action="{{ route('party.leave') }}" method="post">
                        @csrf
                        

                        <table>
                            @foreach ($party->users as $user)
                                <tr>
                                    <td> {{ $user->username }}</td>
                                    <!-- If the user is the host of the party, and the current user being displayed isnt the host -->
                                    @if ($party->host_id == Auth::id() && $user->id != Auth::id())
                                        <td>
                                            <x-button value="{{$user->id}}" name="leaveButton">
                                                Kick
                                            </x-button>
                                            
                                        </td>
                                    @endif
                                </tr>                            
                            @endforeach
                            
                        </table>
                    </form>

                    @if ($party->host_id == Auth::id())
                        <form method="POST" action="{{ route('party.destroy', $party->id) }}">
                            @csrf
                            @method('DELETE')

                            <x-button onclick="return confirm('Are you sure you want to delete this party?');">
                                {{ __('Delete Party')}}
                            </x-button>

                        </form>  
                    @else
                        <form method="POST" action="{{ route('party.leave') }}">
                            @csrf
                            

                            <x-button name="leaveButton" value="{{ Auth::id() }}" onclick="return confirm('Are you sure you want to leave this party?');">
                                {{ __('Leave Party')}}
                            </x-button>

                        </form>  


                    @endif

                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>