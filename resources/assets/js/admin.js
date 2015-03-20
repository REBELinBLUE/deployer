$(function () {
    $('#project').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var project_id = button.data('project-id');

        var modal = $(this);

        var title = 'Add a new project';
        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        var project = {
            id: '',
            name: '',
            repository: '',
            branch: '',
            builds_to_keep: 10,
            url: '',
            build_url: ''
        };

        if (project_id) {
            title = 'Edit project';

            var project = $.grep(projects, function(element) {
                return element.id == project_id;
            });

            project = project[0];

            $('.btn-danger', modal).show();

        }

        $('#project_id').val(project.id);
        $('#project_name').val(project.name);
        $('#project_repository').val(project.repository);
        $('#project_branch').val(project.branch);
        $('#project_builds_to_keep').val(project.builds_to_keep);
        $('#project_url').val(project.url);
        $('#project_build_url').val(project.build_url);

        modal.find('.modal-title span').text(title);
    });

    $('#server').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var server_id = button.data('server-id');

        var modal = $(this);

        var title = 'Add server';
        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        var server = {
            id: '',
            name: '',
            ip_address: '',
            user: '',
            path: ''
        };

        if (server_id) {
            title = 'Edit server';

            var server = $.grep(servers, function(element) {
                return element.id == server_id;
            });

            server = server[0];

            $('.btn-danger', modal).show();
        }

        $('#server_id').val(server.id);
        $('#server_name').val(server.name);
        $('#server_address').val(server.ip_address);
        $('#server_user').val(server.user);
        $('#server_path').val(server.path);

        modal.find('.modal-title span').text(title);
    });

    $('#command').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var command_id = button.data('command-id');
        var step = button.data('step');

        var modal = $(this);

        var action = modal.data('action');

        var title = 'Add command';
        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        var command = {
            id: '',
            name: '',
            script: '',
            user: ''
        };

        $('.command-server').prop('checked', true);

        var servers = [];

        if (command_id) {
            title = 'Edit command';

            var commands = before_commands;
            var servers = before_servers;
            if (step == 'After') {
                commands = after_commands;
                servers = after_servers;
            }

            var command = $.grep(commands, function(element) {
                return element.id == command_id;
            });

            command = command[0];

            $('.btn-danger', modal).show();

            $('.command-server').prop('checked', false);
            $(servers[command.id]).each(function (index, server_id) {
                $('#command_server_' + server_id).prop('checked', true);
            });
        }

        $('#command_id').val(command.id);
        $('#command_step').val(step + ' ' + action);
        $('#command_name').val(command.name);
        $('#command_script').val(command.script);
        $('#command_user').val(command.user);

        modal.find('.modal-title span').text(title);

    });

    $('form .modal-footer button').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        var fields = $('form', dialog).serialize();

        var url = '/' + dialog.data('resource');
        var id = $('form input[name="id"]', dialog).val();
        var method = 'POST';

        if (id !== '') {
            url +=  '/' + id;
            method = 'PUT';
        }

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        $.ajax({
            url: url,
            type: method,
            data: fields,
        }).done(function (data) {
            dialog.modal('hide');
            $('.callout-danger', dialog).hide();

            if (typeof data.redirect != 'undefined') {
                window.location.href = data.redirect;
            }
        }).fail(function (response) {
            $('.callout-danger', dialog).show();

            var errors = response.responseJSON.errors;

            $('form input', dialog).each(function (index, element) {
                element = $(element);

                var name = element.attr('name');

                if (typeof errors[name] != 'undefined') {
                    element.parent('div').addClass('has-error');
                }
            });
        }).always(function () {
            icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
            $('button.close', dialog).show();
            dialog.find('input').removeAttr('disabled');
        });
    });

    // $('.btn-delete').on('click', function (event) {
    //     bootbox.confirm('Are you sure?', function(result) {
    //         console.log(result);
    //     }); 
    // });
     
    $('#new_webhook').on('click', function(event) {
        var target = $(event.currentTarget);
        var project_id = target.data('project-id');
        var icon = $('i', target);

        if ($('.fa-spin', target).length > 0) {
            return;
        }

        target.attr('disabled', 'disabled');

        icon.addClass('fa-spin');

        $.ajax({
            type: 'GET',
            url: '/webhook/' + project_id + '/refresh'
        }).fail(function (response) {

        }).done(function (data) {
            $('#webhook').html(data.url);
        }).always(function () {
            icon.removeClass('fa-spin');
            target.removeAttr('disabled');
        });
    });
    
    $('.btn-test').on('click', function (event) {
        var target = $(event.currentTarget);
        var buttons = $('button', target.parents('tr'));
        var server_id = target.data('server-id');

        if ($('.fa-spin', target).length > 0) {
            return;
        }

        buttons.attr('disabled', 'disabled');

        $('i', target).addClass('fa-spin');

        var label = $('span.label', target.parents('tr'));
        label.removeClass('label-warning label-success label-danger label-primary').addClass('label-warning');
        $('span', label).text('Testing');

        var icon = $('i', label);
        icon.removeClass('fa-check fa-spinner fa-warning fa-question').addClass('fa-spinner');

        checkServer(server_id);

        $.ajax({
            type: 'GET',
            url: '/servers/' + server_id + '/test'
        }).fail(function (response) {
            $('span', label).text('Failed');
            label.removeClass('label-warning').addClass('label-danger');
            icon.removeClass('fa-spinner').addClass('fa-warning');
            buttons.removeAttr('disabled').removeClass('fa-spin');

            clearInterval(callbacks['server_' + server_id]);
        });
    });
    
    $('#log').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var log_id = button.data('log-id');
        var step = $('h3 span', button.parents('.box')).text();
        var modal = $(this);
        var log = $('pre', modal);
        var loader = $('#loading', modal);

        log.hide();
        loader.show();

        $('#action', modal).text(step);
        log.text('');

        $.ajax({
            type: 'GET',
            url: '/logs/' + log_id
        }).done(function (data) {
            var output = data.output;
            // FIXME: There has to be a cleaner way to do this surely?
            output = output.replace(/<\/error>/g, '</span>')
            output = output.replace(/<\/info>/g, '</span>');
            output = output.replace(/<error>/g, '<span class="text-red">')
            output = output.replace(/<info>/g, '<span class="text-default">');

            log.html(output);

            log.show();
            loader.hide();
        }).fail(function() {

        }).always(function() {

        });
    });
});

var callbacks = {};

function checkServer(server_id) {
    var cb = function() {
        $.ajax({
            type: 'GET',
            url: '/servers/' + server_id,
        }).done(function(data) {

            if (data.status != 'Testing') {
                clearInterval(callbacks['server_' + server_id]);

                var label_class = 'primary';
                var icon_class = 'question';

                if (data.status === 'Successful') {
                    label_class = 'success';
                    icon_class = 'check';
                } else if (data.status === 'Failed') {
                    label_class = 'danger';
                    icon_class = 'warning';
                }

                var row = $('#server_' + server_id);
                var label = $('span.label', row);

                $('span', label).text(data.status);
                label.removeClass('label-danger').addClass('label-' + label_class);
                $('i', label).removeClass('fa-spinner').addClass('fa-' + icon_class);
                $('button', row).removeAttr('disabled');
                $('button i', row).removeClass('fa-spin');
            }
        });
    };

    callbacks['server_' + server_id] = setInterval(cb, 2500);
}

function checkDeployment(deployment_id) {
    console.log(deployment_id);
}