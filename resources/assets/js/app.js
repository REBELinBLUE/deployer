$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    jqXHR.setRequestHeader('X-CSRF-Token', $('meta[name="token"]').attr('content'));
});


(function ($) {

    if ($('#timeline').length > 0) {
        console.log('poll for timeline updates')
    }

})(jQuery);