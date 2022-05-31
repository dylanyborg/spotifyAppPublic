const queueButton = document.getElementsByName('queueButton');

//add event listener for every queue button in each row
if(queueButton){
    Array.from(queueButton).forEach(function(element){
        element.addEventListener('click', addToQueue);
    });
}



//function makes AJAX call to spotifyController to fetch more songs from a user lib
    //appends a row to the table for every song in the JSON response
function fetchMoreTracks(){
    var url = "/spotifyController/userLibrary/fetchMoreSongs";

    console.log("about to fetch tracks");

    $.ajax({
        type: 'POST',
        url: url,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (data) {
            console.log("printing json response:");
            console.log( data);
    
            $.each(data.items, function(i, item) {
                $('#tableSongs').append(
                   
                    '<tr>' +
                        '<td class=" w-1/5" style="padding-left: 15px"> <img src=' + item.track.album.images[2].url + '> </td>' +
                        '<td style="padding-left: 10px; padding-right:4px"> <div name="queueButton" data-id=' + item.track.id + '> <p>' + item.track.name + ' </p> <p style="font-size: 12px">' + item.track.artists[0].name + '&#8226' + item.track.album.name + '</p> </div> </td>' +
                        '<td style="padding-right: 10px">' + 

                            '<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">' +
                                '<div @click="open = ! open">' +
                                    '<button class=" bg-orange-600 w-10">' +
                                        '<p> ... <p>' +
                                    '</button> ' +
                                '</div>' +
                        
                                '<div x-show="open"' +
                                        'x-transition:enter="transition ease-out duration-200"' +
                                        'x-transition:enter-start="transform opacity-0 scale-95"' +
                                        'x-transition:enter-end="transform opacity-100 scale-100"' +
                                        'x-transition:leave="transition ease-in duration-75"' +
                                        'x-transition:leave-start="transform opacity-100 scale-100"' +
                                        'x-transition:leave-end="transform opacity-0 scale-95"' +
                                        'class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"' +
                                        'style="display: none;"' +
                                        '@click="open = false">' +
                                    '<div class="rounded-md ring-1 ring-black ring-opacity-5 ring-inset py-1 bg-white">' +
                                        '<div class=" bg-slate-800 text-center mx-1" style="color:white">' +
                                            '<button style="margin-bottom: 5px" type="button" name="queueButton" data-id=' + item.track.id + '>' +
                                                '<p style="font-size: 20px"> Add to queue </p>' +
                                            '</button>' +

                                            '<div style="margin-bottom: 5px">' +
                                                '<a href="/spotifyController/album/' + item.track.album.id + '" ' +
                                                    '<p style="font-size: 20px"> View Album </p>' +
                                                '</a>' +
                                            '</div>' +

                                            '<div>' +
                                                '<a href="/spotifyController/artist/' + item.track.artists[0].id + '" ' +
                                                    '<p style="font-size: 20px"> View Artist </p>' +
                                                '</a>' +
                                            '</div>' +  
                                        '</div' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</td>' +
                    '</tr>' 
          

                );
            });

            //add an event listener to the new buttons
            var newQueueButtons = document.getElementsByName('queueButton');
            if(newQueueButtons){
                Array.from(newQueueButtons).forEach(function(element){
                    element.addEventListener('click', addToQueue);
                });
            }
            
        },
        error: function() {
            console.log("fail");

        }
    });
}

//function to fetch more tracks for a playlist when page is scrolled near the bottom
function fetchMoreTracksForPlaylist(){
    var playlistid = document.getElementById('playlistid').value;
    
    var url = "/spotifyController/fetchMoreSongsForPlaylist";

    console.log("about to fetch tracks for playlist");

    $.ajax({
        type: 'POST',
        url: url,
        data: {"playlistid": playlistid},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (data) {
            console.log("printing json response:");
            console.log( data);

            $.each(data.items, function(i, item) {
                if(!item.is_local){
                    
                    $('#tableSongs').append(
                    
                        '<tr>' +
                            '<td class=" w-1/5" style="padding-left: 15px"> <img src=' +  item.track.album.images[2].url + '> </td>' +
                            '<td style="padding-left: 10px; padding-right:4px"> <div name="queueButton" data-id=' + item.track.id + '> <p>' + item.track.name + ' </p> <p style="font-size: 12px">' + item.track.artists[0].name + '</p> </div> </td>' +
                            '<td style="padding-right: 10px">' + 

                                '<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">' +
                                    '<div @click="open = ! open">' +
                                        '<button class=" bg-orange-600 w-10">' +
                                            '<p> ... <p>' +
                                        '</button> ' +
                                    '</div>' +
                            
                                    '<div x-show="open"' +
                                            'x-transition:enter="transition ease-out duration-200"' +
                                            'x-transition:enter-start="transform opacity-0 scale-95"' +
                                            'x-transition:enter-end="transform opacity-100 scale-100"' +
                                            'x-transition:leave="transition ease-in duration-75"' +
                                            'x-transition:leave-start="transform opacity-100 scale-100"' +
                                            'x-transition:leave-end="transform opacity-0 scale-95"' +
                                            'class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"' +
                                            'style="display: none;"' +
                                            '@click="open = false">' +
                                        '<div class="rounded-md ring-1 ring-black ring-opacity-5 ring-inset py-1 bg-white">' +
                                            '<div class=" bg-slate-800 text-center mx-1" style="color:white">' +
                                                '<button style="margin-bottom: 5px" type="button" name="queueButton" data-id=' + item.track.id + '>' +
                                                    '<p style="font-size: 20px"> Add to queue </p>' +
                                                '</button>' +

                                                '<div style="margin-bottom: 5px">' +
                                                    '<a href="/spotifyController/album/' + item.track.album.id + '" ' +
                                                        '<p style="font-size: 20px"> View Album </p>' +
                                                    '</a>' +
                                                '</div>' +

                                                '<div>' +
                                                    '<a href="/spotifyController/artist/' + item.track.artists[0].id + '" ' +
                                                        '<p style="font-size: 20px"> View Artist </p>' +
                                                    '</a>' +
                                                '</div>' +  
                                            '</div' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
                        '</tr>' 
            

                    );
                }
            });

            //add an event listener to the new buttons
            var newQueueButtons = document.getElementsByName('queueButton');
            if(newQueueButtons){
                Array.from(newQueueButtons).forEach(function(element){
                    element.addEventListener('click', addToQueue);
                });
            }
            
        },
        error: function() {
            console.log("fail");

        }



    });

}

//fucntion makex AJAX call to spotifyController.queue()
// displays a notification on success or fail
function addToQueue(){
    console.log("addToQueue function called");
    var songid = $(this).data('id'); //button had a variable data, inside variable there is id

    var url = "/spotifyController/userLibrary/queue"; //not sure this is good
    console.log("pre ajax");

    //target.style.backgorundcolor = "#6b7280";
    
    this.style.backgroundColor = "#6b7280";

    var currentElement = this;

    $.ajax({
        type: 'POST',
        url: url,
        data: {"songid": songid},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (data) {
            console.log("It worked" + songid);

            currentElement.style.backgroundColor = "#191414";

            var modal = document.getElementById("queueConfirmModal");

            $("h5").html("Song added to queue");

            modal.style.display = "block";

            setTimeout(function(){
                modal.style.display = "none";
            }, 3000);
               
        },
        error: function() {
            console.log("fail");
            
            currentElement.style.backgroundColor = "#191414";

            $("h5").html("Failed to queue song");

            var modal = document.getElementById("queueConfirmModal");
            modal.style.display = "block";

            setTimeout(function(){
                modal.style.display = "none";
            }, 3000);

        }



    });

}