$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    jqXHR.setRequestHeader('X-CSRF-Token', $('meta[name="token"]').attr('content'));
});

var app = app || {};

(function ($) {
    var PENDING   = 1;
    var RUNNING   = 2;
    
    app.listener = io.connect($('meta[name="socket_url"]').attr('content'));

    // Navbar deployment status
    // FIXME: Convert these menus to backbone
    app.listener.on('deployment:App\\Events\\DeploymentStatusChanged', function (data) {
        updateNavBar(data);
    });

    // Add group created and project created events for the sidebar

    app.listener.on('group:App\\Events\\ModelChanged', function (data) {
        $('#group_' + data.model.id).html(data.model.name);
    });

    app.listener.on('project:App\\Events\\ModelChanged', function (data) {
        $('#project_' + data.model.id).html(data.model.name);
    });

    app.listener.on('project:App\\Events\\ModelTrashed', function (data) {
        console.log(data);

        $('#project_' + data.model.id).parent('li').remove();

        if (parseInt(data.model.id) === parseInt(app.project_id)) {
            window.location.href = '/';
        }
    });



    function updateNavBar(data) {
        data.time = moment(data.started.date).format('h:mm:ss A');
        data.url = '/deployment/' + data.deployment_id;

        $('#deployment_info_' + data.deployment_id).remove();
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
        else if (pending === 1) {
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
    }

    $(document).ready(function () {
        if ($('#pending_menu ul.menu li').length > 0) {
            $('#pending_menu').show();
        }

        if ($('#deploying_menu ul.menu li').length > 0) {
            $('#deploying_menu').show();
        }
    });

})(jQuery);