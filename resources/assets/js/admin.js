(function ($) {
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


    /*

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
*/

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
})(jQuery);