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
                <div class="p-6 bg-white border-b border-gray-200 ">
                    <div class="flex justify-between text-center">

                        <div>
                            <!-- Filler div for party title -->
                        </div>
                    
                        <div class=" text-lg flex-grow">
                            <p> Current Party </p>

                        </div>
                        @if ($party->host_id == Auth::id())
                        
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button>

                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Lock party -->
                                    <div class=" mt-4">

                                        
                                        <form action="{{ route('party.lock', $party->id) }}" method="post">
                                            @csrf

                                            @if ($party->isLocked)

                                                <button type="submit" name="lockButton" value="{{$party->id}}">
                                                    {{ __('Unlock Party')}}
                                                </button>
                                                            
                                            @else

                                                <button type="submit" name="lockButton" value="{{$party->id}}">
                                                    {{ __('Lock Party')}}
                                                </button>
                                                
                                            @endif
                                        </form>

                                    </div>

                                    <!-- Edit party -->
                                    <div class=" mt-4">

                                        <button type="button">
                                            <a href="{{ route('party.edit', $party->id) }}">
                                                Edit Party
                                            </a>
                                        </button>
                                    </div>

                                    <!-- delete party -->
                                    <div class=" mt-4 mb-4">

                                    
                                        <form method="POST" action="{{ route('party.destroy', $party->id) }}">
                                            @csrf
                                            @method('DELETE')
                
                                            <button onclick="return confirm('Are you sure you want to delete this party?');">
                                                {{ __('Delete Party')}}
                                            </button>
                
                                        </form> 
                                    </div>
                                    

                                </x-slot>
                            </x-dropdown>

                        @elseif ($party->isLocked)
                            <p> Party is locked </p>
                        @else
                            <div>
                                <!-- Filler div for flexbox -->
                            </div>
                        @endif

                    </div>

                    

                    <!-- If the user is the host, display one of two buttons -->
                    <!-- else if not the host, display an iomage only if the party is locked -->
                    

                    <div>
                        <div>
                            <p> <b> Name: </b> {{ $party->partyName }} </p>

                        </div>
                        <div class=" mt-3">
                            <p> <b> Host: </b> {{ $party->host->username }} </p>
                        </div>
                    </div>

                    <!-- Display party list -->
                    <div class=" mt-8 mb-5">
                        <h2 class=" text-lg">  <u> Users in Party: </u> </h2>
                    
                    
                        <form action="{{ route('party.leave') }}" method="post">
                            @csrf
                            

                            <table  class=" ml-3">
                                @foreach ($party->users as $user)
                                    <tr>
                                        <td> {{ $user->username }}</td>
                                        <!-- If the user is the host of the party, and the current user being displayed isnt the host -->
                                        @if ($party->host_id == Auth::id() && $user->id != Auth::id())
                                            <td>
                                                <!-- Dropdown button -->
                                                <x-button value="{{$user->id}}" name="leaveButton">
                                                    Kick
                                                </x-button>
                                                
                                            </td>
                                        @endif
                                    </tr>                            
                                @endforeach
                                
                            </table>
                        </form>

                    </div>

                    @if ($party->host_id != Auth::id())
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