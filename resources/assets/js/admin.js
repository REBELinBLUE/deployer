$(function () {
    $('#server').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var server_id = button.data('server-id');
        
        var modal = $(this);

        var server = { name: '', ip_address: '', user: '', path: '' };
        var title = 'Add a server';
        if (server_id) {
            title = 'Edit server';

            var server = $.grep(servers, function(element) {
                return element.id == server_id;
            });

            server = server[0];
        }

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

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        setTimeout(function() {
            icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
            dialog.modal('hide');
            $('button.close', dialog).show();
            dialog.find('input').removeAttr('disabled');
        }, 5000);
    });

    $('.btn-delete').on('click', function (event) {
        bootbox.confirm('Are you sure?', function(result) {
            console.log(result);
        }); 
    });
});

