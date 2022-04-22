const queueButton = document.getElementsByClassName('queueButton');

if(queueButton){
    Array.from(queueButton).forEach(function(element){
        element.addEventListener('click', addToQueue);
    });
}

function addToQueue(){
    console.log("addToQueue function called");
    var songid = $(this).data('id)'); //button had a variable data, inside variable there is id

    //preset ajax call?
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var url = "localhost/spotifyController/userLibrary/queue"; //not sure this is good
    $.ajax({
        type: 'POST',
        url: url,
        data: {"songid": songid},
        success: function (data) {
            console.log("It worked" + songid);
            
        }

    });
}