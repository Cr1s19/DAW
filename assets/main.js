$('.toggleModal').on('click', function (e) {
  
  $('#modal').toggleClass('active');
  
});

$('.toggleModal2').on('click', function (e) {
  
  $('#modal2').toggleClass('active');
  
});

$(document).on('click', '#create-album', function(){
  $.post("create.php", { type: "album", name: $('#album-name').val(), user_id: $('#user').val() }, function(data){
    console.log(data);
  });
});

$('.icon-album').hover(
       function(){ $(this).addClass('fontawesome-folder-open-alt').removeClass('fontawesome-folder-close-alt') },
       function(){ $(this).addClass('fontawesome-folder-close-alt').removeClass('fontawesome-folder-open-alt') }
)