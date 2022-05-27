const likedSongButton = document.getElementById('likedSongButton');

//const buttonActionElement = document.getElementById('likedSongButton');
var buttonAction; 
if(likedSongButton){
    buttonAction = document.getElementById('likedSongButton').value;

}

const likedSongButtonImage = document.getElementById('likedSongImage');

//$('#closeLargeSongView').click(hideLargeSongView());
//$('#songInfo').click(showLargeModal());



//const progress_ms = document.getElementById('progress_ms').value;
//const duration_ms = document.getElementById('duration_ms').value;

$(document).ready(function() {
    var progress_ms = document.getElementById('progress_ms').value;
    var duration_ms = document.getElementById('duration_ms').value;
    loadProgressBar(progress_ms, duration_ms);
});

if(likedSongButton){
    if(buttonAction == "delete"){
        likedSongButton.addEventListener('click', deleteFromLibrary);
    
    }
    else if(buttonAction == "add"){
        likedSongButton.addEventListener('click', addToLibrary);

    }

}

function msToMinutesSeconds(millis){
    var minutes = Math.floor(millis/60000);
    var seconds = ((millis % 60000) / 1000).toFixed(0);
    //if seconss is under 10, add a 0
    return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
}

function loadProgressBar(progress_ms, duration_ms){

    var timeLeftMS = duration_ms - progress_ms;

    var percentageLeft = (progress_ms / duration_ms) * 100;
    var percentageOfOneSec = (1000 / duration_ms) * 100;

    var percentageLeftTruncated = Math.trunc(percentageLeft);

   
    var progressElement = document.getElementById('progressBar');
    var largeProgressBarElem = document.getElementById('largeProgressBar');

    var largePlaybackProgress = document.getElementById('largeTimePassed');

    //update the total playtime of the song for largePlaybackView
    var largePlaybackDuration = document.getElementById('largeDuration');
    //convert duration to minuites:seconds
    msToMinutesSeconds(duration_ms);

    largePlaybackDuration.innerHTML = msToMinutesSeconds(duration_ms);


    //set the intiial time progressed
    //can use function but i need seconda and minutes sepearte
    var progressToSeconds = progress_ms / 1000;
    var minutes = progressToSeconds / 60;
    var truncMinutes = Math.trunc(minutes);
    var seconds = Math.trunc(progressToSeconds % 60);

    var timePassedFormatted = truncMinutes + ":" + seconds;

    largePlaybackProgress.innerHTML = timePassedFormatted;
    

    var intervalid = setInterval(move, 1000);

    console.log(percentageOfOneSec);

    function move(){
        if(percentageLeft >= 100){
            clearInterval(intervalid);
            //song has ended
            //call function to fetch a new song and reset the progress bar
            fetchPlayingTrack();
        }
        else{
            percentageLeft += percentageOfOneSec;
            progressElement.style.width = percentageLeft + '%';

            //update the progressbar for largePlaybackView
            largeProgressBarElem.style.width = percentageLeft + '%';
            //update the remaingin time by one second
            //if the seconds is over 60, a minute needs updating
            if(seconds >= 59){
                truncMinutes ++;
                //set seconds to 0
                seconds = 0;
                //save the time
            }
            else{
                //add one second
                seconds++;
            }
            timePassedFormatted = truncMinutes + ":" + (seconds < 10 ? '0' : '') + seconds;

            largePlaybackProgress.innerHTML = timePassedFormatted;
            
            
        }
    }
}

function fetchPlayingTrack(){
    //run an ajax call to the spotify controller to fetch the playing song
    //if successful, update the hidden input values
    console.log("reloading playing track");

    var url = "/spotifyController/refreshPlaybackInfo";

    $.ajax({
        type: 'POST',
        url: url,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (data) {
            console.log("refreshed playback");

            console.log(data);
            console.log(data.playbackInfo.item.name);
            console.log(data.songSaved);

            //update the song information
            var songNameElem = document.getElementById("songName");
            songNameElem.innerHTML = data.playbackInfo.item.name;
            
            //song info for large playback
            var largeModalSongName = document.getElementById("largeModalSongName");
            largeModalSongName.innerHTML = data.playbackInfo.item.name;
            
            //artist for small playback
            var songArtistElem = document.getElementById("songArtist");
            songArtistElem.innerHTML = data.playbackInfo.item.artists[0].name + "&#8226" + data.playbackInfo.item.album.name;
            
            //artists for large playback
            var largeModalArtistName = document.getElementById("largePlaybackArtistNames");
            //loop through the names of the artists, append to a string
            var artistString = "";
            data.playbackInfo.item.artists.forEach(artist => {
                //if at the last aretist in array
                //artist array[length pof array] == current artist
                if(data.playbackInfo.item.artists[data.playbackInfo.item.artists.length - 1] == artist){
                    //artist with no comma
                    artistString = artistString + artist.name;
                }
                else{
                    //artist name with a comma
                    artistString = artistString + artist.name + ", ";
                }
            });
            //add to innerhtlml
            largeModalArtistName.innerHTML = artistString;

            //update the large playback album art
            var largePlaybackAlbumArt = document.getElementById("largePlaybackAlbumArt");
            largePlaybackAlbumArt.src = data.playbackInfo.item.album.images[0].url;

            //update if the user likes the song
            //if the liked song button is available (user is conn to spotify)
          
            if(likedSongButton){
                if(data.songSaved[0]){
                    console.log("song Saved");
                    likedSongButtonImage.src =  '/images/spotifyHeartLiked.svg';
                    likedSongButton.removeEventListener('click', addToLibrary);
                    likedSongButton.addEventListener('click', deleteFromLibrary);
                    
                }
                else{
                    likedSongButtonImage.src =  '/images/spotifyHeartUnliked.svg';
                    likedSongButton.removeEventListener('click', deleteFromLibrary);
                    likedSongButton.addEventListener('click', addToLibrary);
    
                }
            }
            
            

            //update the progress bar
            
            loadProgressBar(data.playbackInfo.progress_ms, data.playbackInfo.item.duration_ms);

        },
        error: function(data) {
            console.log("could not load currewntly playing song");
            console.log(data);
            
        }

    });

}

function deleteFromLibrary(){
    //save the song id to change
    var songid = $(this).data('id');

    console.log("removing song from lib " + songid);


    var url = "/spotifyController/deleteFromLib"; //not sure this is good
    if(songid != ""){
        console.log("song is not null");

        
        $.ajax({
            type: 'POST',
            url: url,
            data: {"songid": songid},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                console.log("Song removed form lib" + songid);

                var modal = document.getElementById("queueConfirmModal");

                $("h5").html("Song Removed from Library");

                likedSongButtonImage.src =  '/images/spotifyHeartUnliked.svg';

                likedSongButton.removeEventListener('click', deleteFromLibrary);
                likedSongButton.addEventListener('click', addToLibrary);

                modal.style.display = "block";

                setTimeout(function(){
                    modal.style.display = "none";
                }, 3000);
                
                
            },
            error: function() {
                console.log("fail");
                $("h5").html("Failed to remove song");

                var modal = document.getElementById("queueConfirmModal");
                modal.style.display = "block";
                //popup.classList.toggle("show");

                setTimeout(function(){
                    modal.style.display = "none";
                }, 3000);

            }

        });
    }
    else{
        console.log("song is null");

        $("h5").html("Cannot save local files");

        var modal = document.getElementById("queueConfirmModal");
        modal.style.display = "block";
        //popup.classList.toggle("show");

        setTimeout(function(){
            modal.style.display = "none";
        }, 3000);

        
    }

}

function addToLibrary(){

    //check if the song is already added

    //save the song id to change
    var songid = $(this).data('id');

    console.log("adding song to lib " + songid);


    var url = "/spotifyController/userLibrary/addToLib"; //not sure this is good

    $.ajax({
        type: 'POST',
        url: url,
        data: {"songid": songid},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (data) {
            console.log("Song added to lib" + songid);

            var modal = document.getElementById("queueConfirmModal");

            $("h5").html("Song Added To Library");

            modal.style.display = "block";

            //change the heart image to the filled in heart
            likedSongButtonImage.src =  '/images/spotifyHeartLiked.svg';

            likedSongButton.removeEventListener('click', addToLibrary);
            likedSongButton.addEventListener('click', deleteFromLibrary);
            
            setTimeout(function(){
                modal.style.display = "none";
            }, 3000);
               
            
        },
        error: function() {
            console.log("fail");
            $("h5").html("Failed to add song");

            var modal = document.getElementById("queueConfirmModal");
            modal.style.display = "block";
            //popup.classList.toggle("show");

            setTimeout(function(){
                modal.style.display = "none";
            }, 3000);

        }

    });

}

function showLargeModal() {
    console.log('displayting song view');
    var largeSongView = document.getElementById("largeSongView");
    //info will be automatically updated at same time the fotter is?
    //start the song name scroll

    largeSongView.style.display = "block";
    scrollSongName();
}

function hideLargeSongView() {
    var largeSongView = document.getElementById("largeSongView");
    largeSongView.style.display = "none";
}

function scrollSongName() {
    console.log("scrollingText in beginning of function");
    var modalSongNameElem = document.getElementById("largeModalSongName");
    //var elemOverflow = modalSongNameElem.style.overflow;

    var totalScrollWidth = modalSongNameElem.scrollWidth;

    var scrollBarPosition = modalSongNameElem.scrollLeft;

    var clientWidth = modalSongNameElem.clientWidth;

    //if scrollwidth = client width, no scroll needed
    if(totalScrollWidth == clientWidth){
        console.log("no scroll necessary");
    }
    else{
        var intervalid = setInterval(move, 50);
        var i = 0;
        function move() {
            //console.log(document.getElementById("largeSongView").style.display);
            //if the element is hidden, end the interval
            if(document.getElementById("largeSongView").style.display == "none"){
                console.log("stopping scroll, playback view hidden");
                clearInterval(intervalid);
            }
            //if at end
            if( (document.getElementById("largeModalSongName").scrollLeft + clientWidth) == totalScrollWidth){
                console.log("scrolled to end");
                //clear the interval
                clearInterval(intervalid);
                setTimeout(function(){
                    modalSongNameElem.scroll({
                        left:0,
                        behavior: 'smooth'
                    });
                    console.log("waited");
                    setTimeout(scrollSongName, 4000);
                }, 4000);

                

                

                //wait a second
                //scroll back top beginning
                //wait a second
                //call the function again

                //wait a second
               
                //reset the scroll
                
                //clearInterval(intervalid);
                //scroll backwards
                //scrollBackwards();
                
            }
            else{
                console.log("scrolling");
                modalSongNameElem.scroll({
                    left:i,
                    behavior: 'smooth'
                });
                i = i + 1;
            }
            
            
        }

        //console.log("done with scrolliogn left");

    }
    //else scroll until the end of the scroll box
    //(scrollBarPos + clientWidth == totalScrollWidth)


    console.log(totalScrollWidth);

    console.log(scrollBarPosition);
    console.log(clientWidth);
    //if(!elemOverflow || elemOverflow === "visible")
}

function scrollBackwards() {
    var modalSongNameElem = document.getElementById("largeModalSongName");
    //var elemOverflow = modalSongNameElem.style.overflow;

    var totalScrollWidth = modalSongNameElem.scrollWidth;

    var scrollBarPosition = modalSongNameElem.scrollLeft;

    var clientWidth = modalSongNameElem.clientWidth;

    var intervalid = setInterval(move, 100);
    var i = 0;

    function move() {
        if(scrollBarPosition == 0){
            clearInterval(intervalid);
            scrollSongName();
        }
        modalSongNameElem.scroll({
            left: -i,
            behavior: 'smooth'
        });
        i = i + 1;
    }
}