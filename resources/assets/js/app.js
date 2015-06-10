$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    jqXHR.setRequestHeader('X-CSRF-Token', $('meta[name="token"]').attr('content'));
});

var app = app || {};

(function ($) {
    var PENDING = 1;
    var RUNNING = 2;

    app.listener = io.connect(window.location.protocol + '//' + window.location.hostname + ':6001');

    // Navbar deployment status
    // FIXME: Convert these menus to backbone
    app.listener.on('deployment-status', function (data) {

        data.time = moment(data.started.date).format('h:mm:ss A');;
        data.url = '/deployments/' + data.id;

        $('#deployment_info_' + data.id).remove();
        $('#pending_menu, #deploying_menu').show();

        var template = _.template($('#deployment_list_template').html());
        var html = template(data);

        if (data.status === PENDING) {
            $('#pending_menu ul.menu').append(html);
        }
        else if (data.status === RUNNING) {
            $('#deploying_menu ul.menu').append(html);
        }

        var pending = $('#pending_menu ul.menu li').length;
        var deploying = $('#deploying_menu ul.menu li').length;

        var pending_label = Lang.nav.multi_pending.replace('%s', pending);
        if (pending === 0) {
            $('#pending_menu').hide();
        }
        else if (deploying === 1) {
            pending_label = Lang.nav.single_pending;
        }

        var deploying_label = Lang.nav.multi_running.replace('%s', deploying);
        if (deploying === 0) {
            $('#deploying_menu').hide();
        }
        else if (deploying === 1) {
            deploying_label = Lang.nav.single_running;
        }

        $('#deploying_menu span.label-warning').html(deploying);
        $('#deploying_menu .header').text(deploying_label);

        $('#pending_menu span.label-info').html(pending);
        $('#pending_menu .header').text(pending_label);
    });

    $(document).ready(function () {
        if ($('#pending_menu ul.menu li').length === 0) {
            $('#pending_menu').hide();
        }

        if ($('#deploying_menu ul.menu li').length === 0) {
            $('#deploying_menu').hide();
        }
    })

})(jQuery);