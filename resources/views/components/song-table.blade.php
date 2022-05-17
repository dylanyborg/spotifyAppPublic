<div>
    <div class="playlistTitle">
        <!-- Dropdown button to select two different libraries -->
        <x-dropdown align="top" width="full" >
            <x-slot name="trigger">
                <div>
                    <button >
                        <p style="font-size: 20px"> {{ $title }} </p>
                    </button>
                </div>
                
            </x-slot>

            <x-slot name="content">
                <div class=" bg-slate-800 text-center mx-1" style="color: white">
                    @isset($lib2)
                        <div>
                            <a href="{{ route('swapLib') }}">
                                <p>
                                    {{ $lib2}}
                                </p>
                            </a>
                        </div>
                    @else 
                        <p>
                            No library to swap to
                        </p>
                    @endisset
                    
                   
                </div>


            </x-slot>

        </x-dropdown>
    </div>

    <div class="listOfSongs">
        {{ $slot }}
    </div>
</div>
