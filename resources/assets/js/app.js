$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    jqXHR.setRequestHeader('X-CSRF-Token', $('meta[name="token"]').attr('content'));
});

var app = app || {};

(function ($) {
    var FINISHED     = 0;
    var PENDING      = 1;
    var DEPLOYING    = 2;
    var FAILED       = 3;
    var NOT_DEPLOYED = 4;

    app.project_id = app.project_id || null;

    app.listener = io.connect($('meta[name="socket_url"]').attr('content'));

    // Navbar deployment status
    // FIXME: Convert these menus to backbone
    // TODO: Update the timeline
    app.listener.on('deployment:App\\Events\\DeploymentStatusChanged', function (data) {
        updateNavBar(data);
    });

    // Add group created and project created events for the sidebar

    app.listener.on('group:App\\Events\\ModelChanged', function (data) {
        $('#sidebar_group_' + data.model.id).html(data.model.name);
    });

    app.listener.on('project:App\\Events\\ModelChanged', function (data) {
        $('#sidebar_project_' + data.model.id).html(data.model.name);

        var icon_class = 'question-circle';
        var label_class = 'primary';
        var label = Lang.projects.status.not_deployed;

        var status = parseInt(data.model.status);

        if (status === FINISHED) {
            icon_class = 'check';
            label_class = 'success';
            label = Lang.projects.status.finished;
        } else if (status === DEPLOYING) {
            icon_class = 'spinner fa-pulse';
            label_class = 'warning';
            label = Lang.projects.status.deploying;
        } else if (status === FAILED) {
            icon_class = 'warning';
            label_class = 'danger';
            label = Lang.projects.status.failed;
        } else if (status === PENDING) {
            icon_class = 'clock-o';
            label_class = 'info';
            label = Lang.projects.status.pending;
        }

        var project = $('#project_' + data.model.id);

        $('td:first a', project).text(data.model.name);
        $('td:nth-child(2)', project).text(moment(data.model.last_run).format('Do MMM YYYY h:mm:ss A'));
        $('td:nth-child(3) span.label', project).attr('class', 'label label-' + label_class)
        $('td:nth-child(3) span.label i', project).attr('class', 'fa fa-' + icon_class);
        $('td:nth-child(3) span.label span', project).text(label);
    });

    app.listener.on('project:App\\Events\\ModelTrashed', function (data) {
        $('#sidebar_project_' + data.model.id).parent('li').remove();

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
        else if (data.status === DEPLOYING) {
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