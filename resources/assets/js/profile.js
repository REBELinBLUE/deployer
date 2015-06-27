var app = app || {};

(function ($) {
    $('#request-change-email').on('click',function(){
        var box = $(this).parents('.box');
        box.children('.overlay').removeClass('hide');
        $.post('/profile/email',function(res){
            if(res == 'success'){
                box.children('.overlay').addClass('hide');
                box.find('.help-block').removeClass('hide');
            }
        });
    });
})(jQuery);