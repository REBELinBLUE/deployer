$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    jqXHR.setRequestHeader('X-CSRF-Token', $('meta[name="token"]').attr('content'));
});

var app = app || {};

(function ($) {

    // if ($('#timeline').length > 0) {
    //     console.log('poll for timeline updates')
    // }

    app.listener = io.connect('http://deploy.app:6001');

})(jQuery);