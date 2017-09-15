var app = app || {};

(function ($) {
    // FIXME: This seems very wrong
    $('#shared_server').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.get('servers.create');

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');
        $('.label-danger', modal).remove();
        $('#add-server-command', modal).hide();

        if (button.hasClass('btn-edit')) {
            title = Lang.get('servers.edit');
            $('.btn-danger', modal).show();
        } else {
            $('#shared_server_id').val('');
            $('#shared_server_name').val('');
            $('#shared_server_user').val('');
            $('#shared_server_path').val('');
            $('#shared_server_address').val('');
            $('#shared_server_port').val('22');
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#shared_server button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var server_id = $('#shared_server_id').val();

        if (server_id) {
            var serverTemplate = app.ServerTemplates.get(server_id);
        } else {
            var serverTemplate = new app.ServerTemplate();
        }

        serverTemplate.save({
            name:         $('#shared_server_name').val(),
            ip_address:   $('#shared_server_address').val(),
            port:         $('#shared_server_port').val(),
            user:         $('#shared_server_user').val(),
            path:         $('#shared_server_path').val(),
            add_commands: $('#shared_server_commands').is(':checked')
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!server_id) {
                    app.ServerTemplates.add(response);
                }
            },
            error: function(model, response, options) {
                $('.callout-danger', dialog).show();

                var errors = response.responseJSON;

                $('.has-error', dialog).removeClass('has-error');
                $('.label-danger', dialog).remove();

                $('form input', dialog).each(function (index, element) {
                    element = $(element);

                    var name = element.attr('name');

                    if (typeof errors[name] !== 'undefined') {
                        var parent = element.parents('div.form-group');
                        parent.addClass('has-error');
                        parent.append($('<span>').attr('class', 'label label-danger').text(errors[name]));
                    }
                });

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        });
    });

    app.ServerTemplate = Backbone.Model.extend({
        urlRoot: '/admin/servers'
    });

    var ServerTemplates = Backbone.Collection.extend({
        model: app.ServerTemplate
    });

    app.ServerTemplates = new ServerTemplates();

    app.ServerTemplatesTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#shared_server_list tbody');

            $('#no_shared_servers').show();
            $('#shared_server_list').hide();

            this.listenTo(app.ServerTemplates, 'add', this.addOne);
            this.listenTo(app.ServerTemplates, 'reset', this.addAll);
            this.listenTo(app.ServerTemplates, 'remove', this.addAll);
            this.listenTo(app.ServerTemplates, 'all', this.render);

            app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
                var serverTemplate = app.ServerTemplates.get(parseInt(data.model.id));

                if (serverTemplate) {
                    serverTemplate.set(data.model);
                }
            });

            app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
                app.ServerTemplates.add(data.model);
            });

            app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
                var serverTemplate = app.ServerTemplates.get(parseInt(data.model.id));

                if (serverTemplate) {
                    app.ServerTemplates.remove(serverTemplate);
                }
            });
        },
        render: function () {
            if (app.ServerTemplates.length) {
                $('#no_shared_servers').hide();
                $('#shared_server_list').show();
            } else {
                $('#no_shared_servers').show();
                $('#shared_server_list').hide();
            }
        },
        addOne: function (serverTemplate) {
            var view = new app.ServerTemplateView({
                model: serverTemplate
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.ServerTemplates.each(this.addOne, this);
        }
    });

    app.ServerTemplateView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editServer'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#shared_server-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            //data.project_count =;

            this.$el.html(this.template(data));

            return this;
        },
      editServer: function() {
            // FIXME: Sure this is wrong?
            $('#shared_server_id').val(this.model.id);
            $('#shared_server_name').val(this.model.get('name'));
            $('#shared_server_address').val(this.model.get('ip_address'));
            $('#shared_server_port').val(this.model.get('port'));
            $('#shared_server_user').val(this.model.get('user'));
            $('#shared_server_path').val(this.model.get('path'));
        }
    });
})(jQuery);
