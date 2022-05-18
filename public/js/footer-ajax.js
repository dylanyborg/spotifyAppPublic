const likedSongButton = document.getElementById('likedSongButton');

//const buttonActionElement = document.getElementById('likedSongButton');
var buttonAction; 
if(likedSongButton){
    buttonAction = document.getElementById('likedSongButton').value;

}

const likedSongButtonImage = document.getElementById('likedSongImage');

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

function loadProgressBar(progress_ms, duration_ms){

    var timeLeftMS = duration_ms - progress_ms;

    var percentageLeft = (progress_ms / duration_ms) * 100;
    var percentageOfOneSec = (1000 / duration_ms) * 100;

    var percentageLeftTruncated = Math.trunc(percentageLeft);

   

    var intervalid = setInterval(move, 1000);

    var progressElement = document.getElementById('progressBar');
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

            var songArtistElem = document.getElementById("songArtist");
            songArtistElem.innerHTML = data.playbackInfo.item.artists[0].name + "&#8226" + data.playbackInfo.item.album.name;

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