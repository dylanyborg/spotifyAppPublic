<div id="largeSongView" class="largeModal">
        <div class="largeModal-content">
            <div style=" display: flex; justify-content:space-between; width: 100%; height:10%; color:white; font-size:20px; padding: 20px 20px 0 20px; ">
                <div id="closeLargeSongView">
                    ^
                </div>
                <div>
                    <img style="max-width: 60%; margin-left:auto; margin-right:auto" src="{{ asset('images/Spotify_Logo_RGB_Green.png') }}" alt="Spotify">

                </div>
                <div>
                    ...
                </div>
            </div>

            <div name="albumImage" style="width: 90%; margin:auto; padding-top:35px">
                <img id="largePlaybackAlbumArt" src="{{ $currentSong->item->album->images[0]->url}}" alt="">
            </div>

            <div name="songInfo" style="max-height: 20%; padding: 25px 10px 10px 10px">
                <p id="largeModalSongName" class="scrollingText" style="font-size: 20px; padding-bottom:5px; white-space: nowrap; overflow:scroll; ">
                    <b> {{ $currentSong->item->name}} </b> 
                </p>
                <p id="largePlaybackArtistNames" style="padding-top: 5px">
                    @foreach ($currentSong->item->artists as $artist)
                        @if ($loop->last)
                            {{ $artist->name}}
                        @else 
                            {{ $artist->name}},
                        @endif
                        
                    @endforeach
                </p>
                
            </div>

            <div style="padding: 10px 25px 0 25px;">
                <div name="progressBar" id="largeProgressBarOutline" style="height: 7px; background-color:rgb(192, 72, 29);  width:100% ">
                    <div id="largeProgressBar" style="width: 10%; height:7px; background-color:rgb(255, 102, 0)">

                    </div>
                </div>
                <div style="display: flex; justify-content:space-between; padding-top:5px">
                    <div id="largeTimePassed">
                        
    
                    </div>
                    <div id="largeDuration">
    
                    </div>
                </div>
            </div>
            
            <div>
                heart 
            </div>


        </div>

        <script type="text/javascript">
        /*
            var modalSongNameElem = document.getElementById("largeModalSongName");
            var totalScrollWidth = modalSongNameElem.scrollWidth;

            var scrollBarPosition = modalSongNameElem.scrollLeft;

            var clientWidth = modalSongNameElem.clientWidth;
            if(totalScrollWidth == clientWidth){
                console.log("no scroll necessary");
            }
            else{
                var intervalid = setInterval(move, 100);
                var i = 0;
                function move() {
                    //if at end
                    if( (scrollBarPosition + clientWidth) == totalScrollWidth){
                        clearInterval(intervalid);
                        //scroll backwards
                        scrollBackwards();
                    }
                    modalSongNameElem.scroll({
                        left:i,
                        behavior: 'smooth'
                    });
                    i = i + 1;
                }
            }
            */
        </script>
</div>