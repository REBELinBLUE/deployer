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
});

