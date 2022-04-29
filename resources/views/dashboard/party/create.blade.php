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
                    <!-- If the user is logged inbto spotiofy, let them create a party -->

                    @isset(Auth::user()->spotifyUserAccessToken)
                        <div>
                            <h1 class="text-lg"> Create a party </h1>

                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />
                            

                            <form method="POST" action="{{ route('party.store') }}">
                                @csrf
                                <div>
                                    <label for="partyName">Party Name:</label> 
                                    <input type="text" name="partyName" id="partyName" value="{{ old('partyName') }}" required autofocus> 
                                </div>

                                <div>
                                    <label for="password">Password:</label> 
                                    <input type="text" name="password" id="password" required>
                                </div>
                                
                                <div>
                                    <label for="password_confirmation">Repeat:</label>
                                    <input type="text" name="password_confirmation" id="password_confirmation" required>
                                </div>

                                <div>
                                    <h3> Show library to party guests?</h3>
                                    <label for="yesRadio">YES</label>
                                    <input type="radio" name="hideHostLib" id="yesRadio" value="1"><br>

                                    <label for="noRadio">NO</label>
                                    <input type="radio" name="hideHostLib" id="noRadio" value="2">
                                </div>

                                <div>
                                    <x-button>
                                        Create Party!
                                    </x-button>
                                </div>
                                
                            </form>
                        </div>
                    @endisset

                    <div>
                        <!-- Join Party -->
                        <h1 class="text-lg"> Join a party </h1>
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                                    

                        <form method="POST" action="{{ route('party.join') }}">
                            @csrf
                            <div>
                                <label for="partyName">Party Name:</label> 
                                <input type="text" name="partyName" id="partyName" value="{{ old('partyName') }}" required autofocus> 
                            </div>

                            <div>
                                <label for="password">Password:</label> 
                                <input type="text" name="password" id="password" required>
                            </div> 

                            <div>
                                <x-button>
                                    Join Party!
                                </x-button>
                            </div>
                            
                        </form>

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>