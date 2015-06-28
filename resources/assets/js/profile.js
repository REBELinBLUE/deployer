var app = app || {};

(function ($) {
    var cropperData = {};
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

    $('.avatar>img').cropper({
        aspectRatio: 1 / 1,
        preview: ".avatar-preview",
        crop: function(data) {
            cropperData.dataX =Math.round(data.x);
            cropperData.dataY =Math.round(data.y);
            cropperData.dataHeight =Math.round(data.height);
            cropperData.dataWidth =Math.round(data.width);
            cropperData.dataRotate =Math.round(data.rotate);
        },
        built: function(){
            $('#upload-overlay').addClass('hide');
        }
    });
    var uploader = new Uploader({
        trigger: '#upload',
        name: 'file',
        action: '/profile/upload',
        accept: 'image/*',
        data: {'_token': $('meta[name="token"]').attr('content')},
        multiple: false,
        change: function(){
            $('#upload-overlay').removeClass('hide');
            this.submit();
        },
        error: function(file) {
            if(file.responseJSON.file){
                alert(file.responseJSON.file.join(''));
            }else if(file.responseJSON.error){
                alert(file.responseJSON.error.message);
            }
            $('#upload-overlay').addClass('hide');
        },
        success: function(response) {
            if(response.message ==='success'){
                $('.avatar>img').cropper('replace',response.image);
                cropperData.path = response.path;
            }
        }
    });
    $('#save-avatar').click(function(){
        $('#upload-overlay').removeClass('hide');
        $('.avatar-message .alert').addClass('hide');
        $.post('/profile/avatar',cropperData).success(function(resp){
            $('#upload-overlay').addClass('hide');
            if(resp.image){
                $('.avatar-message .alert.alert-success').removeClass('hide');
            }else{
                $('.avatar-message .alert.alert-danger').removeClass('hide');
            }
        });
    });
})(jQuery);