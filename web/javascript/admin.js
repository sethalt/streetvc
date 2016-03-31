$(document).ready(function(){
    $('.message.fade .close').on('click', function() {
        $(this).closest('.message').fadeOut();
    });
    $('.message.up .close').on('click', function() {
        $(this).closest('.message').slideUp();
    });
    $('.ui.video').video();
    $('.ui.checkbox').checkbox();
});
