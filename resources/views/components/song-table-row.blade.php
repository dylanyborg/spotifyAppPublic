<tr>
    <td class=" w-1/5" style="padding-left: 15px">
        <img src={{ $albumArt }} alt="">
    </td>
    <td class ='wideCell' style="padding-left: 10px; padding-right:4px">
        <div name="queueButton" data-id={{$trackid}}>

            <p>
                {{ $trackName }}
            </p>
            <p style="font-size: 12px">
                {{ $artistName }} 
                @isset($albumName)
                &#8226 {{ $albumName}}
                @endisset
            </p>
        </div>
    </td>

    <td style="padding-right: 10px">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class=" bg-orange-600 w-10">
                    <p > ... </p>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class=" bg-slate-800 text-center mx-1">
                    <!-- Add to queue -->
                    <button style="margin-bottom: 5px" type="button" name="queueButton" data-id={{$trackid}}>
                        <p style="font-size: 20px">
                            {{$queueButton}}
                        </p>
                    </button>

                    @isset($albumLink)
                        <div style="margin-bottom: 5px">
                            <a href="{{ route('album.show', $albumid) }} ">
                                <p style="font-size: 20px">
                                    {{$albumLink}}
                                </p>
                            </a>
                        </div>
                    @endisset

                    @isset($artistLink)
                        <div>
                            <a href="{{ route('artist.show', $artistid) }} ">
                                <p style="font-size: 20px">
                                    {{$artistLink}}
                                </p>
                            </a>                                    
                        </div>
                    @endisset

                    
                </div>


            </x-slot>

        </x-dropdown>


        
    </td>
</tr>