/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************!*\
  !*** ./resources/js/footer.js ***!
  \********************************/
var likedSongButton = document.getElementById('likedSongButton'); //const buttonActionElement = document.getElementById('likedSongButton');

var buttonAction;

if (likedSongButton) {
  buttonAction = document.getElementById('likedSongButton').value;
}

var likedSongButtonImage = document.getElementById('likedSongImage'); //$('#closeLargeSongView').click(hideLargeSongView());
//$('#songInfo').click(showLargeModal());
//const progress_ms = document.getElementById('progress_ms').value;
//const duration_ms = document.getElementById('duration_ms').value;

$(document).ready(function () {
  var progress_ms = document.getElementById('progress_ms').value;
  var duration_ms = document.getElementById('duration_ms').value;
  //add a click event to the id songInfo
  document.getElementById("songInfo").addEventListener('click', showLargeModal);
  //add click event for hiding the large playback view
  document.getElementById("closeLargeSongView").addEventListener('click', hideLargeSongView);

  loadProgressBar(progress_ms, duration_ms);
});

if (likedSongButton) {
  if (buttonAction == "delete") {
    likedSongButton.addEventListener('click', deleteFromLibrary);
  } else if (buttonAction == "add") {
    likedSongButton.addEventListener('click', addToLibrary);
  }
}

function msToMinutesSeconds(millis) {
  var minutes = Math.floor(millis / 60000);
  var seconds = (millis % 60000 / 1000).toFixed(0); //if seconss is under 10, add a 0

  return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
}

function loadProgressBar(progress_ms, duration_ms) {
  var percentageLeft = progress_ms / duration_ms * 100;
  var percentageOfOneSec = 1000 / duration_ms * 100;
  var progressElement = document.getElementById('progressBar');
  var largeProgressBarElem = document.getElementById('largeProgressBar'); //track progress and duration element

  var largePlaybackProgress = document.getElementById('largeTimePassed');
  var largePlaybackDuration = document.getElementById('largeDuration');
  largePlaybackDuration.innerHTML = msToMinutesSeconds(duration_ms); //set the intiial time progressed
  //can use function but i need seconda and minutes sepearte
  //var progressToSeconds = progress_ms / 1000;
  //var minutes = progressToSeconds / 60;
  //var truncMinutes = Math.trunc(minutes);
  //var seconds = Math.trunc(progressToSeconds % 60);

  var minutes = Math.floor(progress_ms / 60000);
  var seconds = (progress_ms % 60000 / 1000).toFixed(0); // var timePassedFormatted = truncMinutes + ":" + (seconds < 10 ? '0' : '') + seconds;

  largePlaybackProgress.innerHTML = minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
  var intervalid = setInterval(move, 1000);

  function move() {
    if (percentageLeft >= 100) {
      clearInterval(intervalid); //song has ended
      //call function to fetch a new song and reset the progress bar

      fetchPlayingTrack();
    } else {
      percentageLeft += percentageOfOneSec;
      progressElement.style.width = percentageLeft + '%'; //update the progressbar for largePlaybackView

      largeProgressBarElem.style.width = percentageLeft + '%'; //update the remaingin time by one second
      //if the seconds is over 60, a minute needs updating

      if (seconds >= 59) {
        minutes++;
        seconds = 0;
      } else {
        //add one second
        seconds++;
      } //timePassedFormatted = minutes + ":" + (seconds < 10 ? '0' : '') + seconds;


      largePlaybackProgress.innerHTML = minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
    }
  }
} //function makes AJAX call to spotifyController.refreshPlaybackInfo()
//updates both the small and large playback views, calls loadProgressBar()


function fetchPlayingTrack() {
  //run an ajax call to the spotify controller to fetch the playing song
  //if successful, update the hidden input values
  console.log("reloading playing track");
  var url = "/spotifyController/refreshPlaybackInfo";
  $.ajax({
    type: 'POST',
    url: url,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function success(data) {
      console.log("refreshed playback");
      console.log(data);
      console.log(data.playbackInfo.item.name);
      console.log(data.songSaved); //update the song information

      var songNameElem = document.getElementById("songName");
      songNameElem.innerHTML = data.playbackInfo.item.name; //song info for large playback

      var largeModalSongName = document.getElementById("largeModalSongName");
      largeModalSongName.innerHTML = data.playbackInfo.item.name; //artist for small playback

      var songArtistElem = document.getElementById("songArtist");
      songArtistElem.innerHTML = data.playbackInfo.item.artists[0].name + "&#8226" + data.playbackInfo.item.album.name; //artists for large playback

      var largeModalArtistName = document.getElementById("largePlaybackArtistNames"); //loop through the names of the artists, append to a string

      var artistString = "";
      data.playbackInfo.item.artists.forEach(function (artist) {
        //if at the last aretist in array
        //artist array[length pof array] == current artist
        if (data.playbackInfo.item.artists[data.playbackInfo.item.artists.length - 1] == artist) {
          //artist with no comma
          artistString = artistString + artist.name;
        } else {
          //artist name with a comma
          artistString = artistString + artist.name + ", ";
        }
      });
      largeModalArtistName.innerHTML = artistString; //update the large playback album art

      var largePlaybackAlbumArt = document.getElementById("largePlaybackAlbumArt");
      largePlaybackAlbumArt.src = data.playbackInfo.item.album.images[0].url; //update if the user likes the song
      //if the liked song button is available (user is conn to spotify)

      if (likedSongButton) {
        if (data.songSaved[0]) {
          console.log("song Saved");
          likedSongButtonImage.src = '/images/spotifyHeartLiked.svg';
          likedSongButton.removeEventListener('click', addToLibrary);
          likedSongButton.addEventListener('click', deleteFromLibrary);
        } else {
          likedSongButtonImage.src = '/images/spotifyHeartUnliked.svg';
          likedSongButton.removeEventListener('click', deleteFromLibrary);
          likedSongButton.addEventListener('click', addToLibrary);
        }
      } //update the progress bar


      loadProgressBar(data.playbackInfo.progress_ms, data.playbackInfo.item.duration_ms);
    },
    error: function error(data) {
      console.log("could not load currewntly playing song");
      console.log(data);
    }
  });
} //function to delete the currently playing song from users lib


function deleteFromLibrary() {
  //save the song id to change
  var songid = $(this).data('id');
  console.log("removing song from lib " + songid);
  var url = "/spotifyController/deleteFromLib"; //not sure this is good

  if (songid != "") {
    console.log("song is not null");
    $.ajax({
      type: 'POST',
      url: url,
      data: {
        "songid": songid
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function success(data) {
        console.log("Song removed form lib" + songid);
        var modal = document.getElementById("queueConfirmModal");
        $("h5").html("Song Removed from Library");
        likedSongButtonImage.src = '/images/spotifyHeartUnliked.svg';
        likedSongButton.removeEventListener('click', deleteFromLibrary);
        likedSongButton.addEventListener('click', addToLibrary);
        modal.style.display = "block";
        setTimeout(function () {
          modal.style.display = "none";
        }, 3000);
      },
      error: function error() {
        console.log("fail");
        $("h5").html("Failed to remove song");
        var modal = document.getElementById("queueConfirmModal");
        modal.style.display = "block";
        setTimeout(function () {
          modal.style.display = "none";
        }, 3000);
      }
    });
  } else {
    console.log("song is null");
    $("h5").html("Cannot save local files");
    var modal = document.getElementById("queueConfirmModal");
    modal.style.display = "block";
    setTimeout(function () {
      modal.style.display = "none";
    }, 3000);
  }
} //function makes an jaax call to the addToLib route
//adds a song to the current users library
//on success, displays a confirmation message and changes the heart


function addToLibrary() {
  //check if the song is already added
  //save the song id to change
  var songid = $(this).data('id');
  console.log("adding song to lib " + songid);
  var url = "/spotifyController/userLibrary/addToLib"; //not sure this is good

  $.ajax({
    type: 'POST',
    url: url,
    data: {
      "songid": songid
    },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function success(data) {
      console.log("Song added to lib" + songid);
      var modal = document.getElementById("queueConfirmModal");
      $("h5").html("Song Added To Library");
      modal.style.display = "block"; //change the heart image to the filled in heart

      likedSongButtonImage.src = '/images/spotifyHeartLiked.svg';
      likedSongButton.removeEventListener('click', addToLibrary);
      likedSongButton.addEventListener('click', deleteFromLibrary);
      setTimeout(function () {
        modal.style.display = "none";
      }, 3000);
    },
    error: function error() {
      console.log("fail");
      $("h5").html("Failed to add song");
      var modal = document.getElementById("queueConfirmModal");
      modal.style.display = "block"; //popup.classList.toggle("show");

      setTimeout(function () {
        modal.style.display = "none";
      }, 3000);
    }
  });
} //data for the large playback view is updated at same time as small playback view


function showLargeModal() {
  console.log('displayting song view');

  var largeSongView = document.getElementById("largeSongView");
  largeSongView.style.display = "block";
  scrollSongName();
}

function hideLargeSongView() {
  //modify event listener function
  document.getElementById("songInfo").removeEventListener('click', showLargeModal);
  document.getElementById("songInfo").addEventListener('click', hideLargeSongView);
  var largeSongView = document.getElementById("largeSongView");
  largeSongView.style.display = "none";
} //function to scroll the name of the song, if it doesnt fit on one line


function scrollSongName() {
  console.log("scrollingText in beginning of function");
  var modalSongNameElem = document.getElementById("largeModalSongName"); //var elemOverflow = modalSongNameElem.style.overflow;

  var totalScrollWidth = modalSongNameElem.scrollWidth;
  var scrollBarPosition = modalSongNameElem.scrollLeft;
  var clientWidth = modalSongNameElem.clientWidth; //if scrollwidth = client width, no scroll needed

  if (totalScrollWidth == clientWidth) {
    console.log("no scroll necessary");
  } else {
    var move = function move() {
      //if the element is hidden, end the interval: no need to scroll
      if (document.getElementById("largeSongView").style.display == "none") {
        console.log("stopping scroll, playback view hidden");
        clearInterval(intervalid);
      } //if at end


      if (document.getElementById("largeModalSongName").scrollLeft + clientWidth == totalScrollWidth) {
        console.log("scrolled to end"); //clear the scroll interval

        clearInterval(intervalid); //after 4 seconds, scroll to the beginning

        setTimeout(function () {
          modalSongNameElem.scroll({
            left: 0,
            behavior: 'smooth'
          });
          console.log("waited");
          setTimeout(scrollSongName, 4000);
        }, 4000);
      } else {
        //scroll to the right
        console.log("scrolling");
        modalSongNameElem.scroll({
          left: i,
          behavior: 'smooth'
        });
        i = i + 1;
      }
    };

    var intervalid = setInterval(move, 50);
    var i = 0;
  }
}
/******/ })()
;