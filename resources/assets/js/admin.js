$(function () {
    $('#server').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var server_id = button.data('server-id');

        var modal = $(this);

        var server = { id: '', name: '', ip_address: '', user: '', path: '' };
        var title = 'Add a server';
        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

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
        }).done(function(data) {
            dialog.modal('hide');
            $('.callout-danger', dialog).hide();
        }).fail(function(response) {
            $('.callout-danger', dialog).show();

            var errors = response.responseJSON.errors;

            $('form input', dialog).each(function (index, element) {
                element = $(element);

                var name = element.attr('name');

                if (typeof errors[name] != 'undefined') {
                    element.parent('div').addClass('has-error');
                }
            });
        }).always(function() {
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
        }).fail(function(response) {
            $('span', label).text('Failed');
            label.removeClass('label-warning').addClass('label-danger');
            icon.removeClass('fa-spinner').addClass('fa-warning');
            buttons.removeAttr('disabled').removeClass('fa-spin');

            clearInterval(callbacks['server_' + server_id]);
        });
    });


    var callbacks = {};

    function checkServer(server_id) {
        var cb = function() {
            $.ajax({
                type: 'GET',
                url: '/servers/' + server_id,
            }).done(function(data) {
                console.log(data);

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
});

