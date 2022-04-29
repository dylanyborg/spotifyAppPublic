const queueButton = document.getElementsByClassName('queueButton');
//const queueButton = document.querySelectorAll('[id=queueButton]');


if(queueButton){
    Array.from(queueButton).forEach(function(element){
        element.addEventListener('click', addToQueue);
    });
}



function addToQueue(){
    console.log("addToQueue function called");
    var songid = $(this).data('id'); //button had a variable data, inside variable there is id

    

    var url = "/spotifyController/userLibrary/queue"; //not sure this is good
    console.log("pre ajax");
    $.ajax({
        type: 'POST',
        url: url,
        data: {"songid": songid},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (data) {
            console.log("It worked" + songid);

            var modal = document.getElementById("queueConfirmModal");
            modal.style.display = "block";
            //popup.classList.toggle("show");

            setTimeout(function(){
                modal.style.display = "none";
            }, 3000);
               
            
        },
        error: function() {
            console.log("fail");
        }

    });
}