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

                     <p> {{ $party->partyName }} </p>

                     {{ $party->host_id }}

                     

                     <p>
                         hiding hose lib?: <br>
                        {{ $party->hideHostLibrary }}

                     </p>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>