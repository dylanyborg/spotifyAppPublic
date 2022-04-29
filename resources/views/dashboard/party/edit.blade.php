<x-app-layout>
    <x-slot name="header">
        <h2>
            <div class="flex justify-between">
                
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
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form action="{{ route('party.update', $party->id) }}" method="post">
                        @csrf
                        @method('PATCH')

                        <!-- change party name -->
                        <label for="partyName">Party Name:</label>
                        <input type="text" name="partyName" id="partyName" value="{{ $party->partyName }}">

                        <!-- change password -->
                        <label for="password">Password:</label> 
                        <input type="text" name="password" id="password">

                        <!-- confirm password -->
                        <label for="password_confirmation">Repeat:</label>
                        <input type="text" name="password_confirmation" id="password_confirmation">

                        <h3> Show library to party guests?</h3>
                        @if ($party->hideHostLibrary) 
                            
                            <label for="yesRadio">YES</label>
                            <input type="radio" name="hideHostLib" id="yesRadio" value="1"><br>

                            <label for="noRadio">NO</label>
                            <input type="radio" name="hideHostLib" id="noRadio" value="2" checked>
                            
                        @else

                            <label for="yesRadio">YES</label>
                            <input type="radio" name="hideHostLib" id="yesRadio" value="1" checked><br>

                            <label for="noRadio">NO</label>
                            <input type="radio" name="hideHostLib" id="noRadio" value="2" >

                        @endif

                        <x-button>
                            Save Changes
                        </x-button>

                        
                   

                    </form>                              

                    @if ($party->host_id == Auth::id())
                        <form method="POST" action="{{ route('party.destroy', $party->id) }}">
                            @csrf
                            @method('DELETE')

                            <x-button onclick="return confirm('Are you sure you want to delete this party?');">
                                {{ __('Delete Party')}}
                            </x-button>

                        </form>                     
                    @endif

                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>