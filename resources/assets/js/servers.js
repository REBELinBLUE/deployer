var app = app || {};

(function ($) {
    var SUCCESSFUL = 0;
    var UNTESTED   = 1;
    var FAILED     = 2;
    var TESTING    = 3;

    $('#server_list table').sortable({
        containerSelector: 'table',
        itemPath: '> tbody',
        itemSelector: 'tr',
        placeholder: '<tr class="placeholder"/>',
        delay: 500,
        onDrop: function (item, container, _super) {
            _super(item, container);

            var ids = [];
            $('tbody tr td:first-child', container.el[0]).each(function (idx, element) {
                ids.push($(element).data('server-id'));
            });

            $.ajax({
                url: '/servers/reorder',
                method: 'POST',
                data: {
                    servers: ids
                }
            });
        }
    });

    // FIXME: This seems very wrong
    $('#server').on('show.bs.modal', function (event) {
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
            $('#server_id').val('');
            $('#server_name').val('');
            $('#server_address').val('');
            $('#server_port').val('22');
            $('#server_user').val('');
            $('#server_path').val('');
            $('#server_deploy_code').prop('checked', true);
            $('#add-server-command', modal).show();
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#server #server_name').autocomplete({
        serviceUrl: '/servers/autocomplete',
        dataType: 'json',
        noCache: true,
        preserveInput: true,
        transformResult: function (response) {
            return {
                suggestions: $.map(response.suggestions, function (dataItem) {
                    var value = dataItem.name + ' (' + dataItem.user + '@' + dataItem.ip_address + ')';
                    return {
                      value: value,
                      data: dataItem
                    };
                })
            };
        },
        onSelect: function (suggestion) {
            var server = suggestion.data;
            $('#server_name').val(server.name);
            $('#server_address').val(server.ip_address);
            $('#server_port').val(server.port);
            $('#server_user').val(server.user);
            $('#server_path').val(server.path);
            $('#server_deploy_code').prop('checked', server.deploy_code);
        }
    });

    // FIXME: This seems very wrong
    $('#server button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var server = app.Servers.get($('#server_id').val());

        server.destroy({
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            },
            error: function() {
                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        });
    });

    // FIXME: This seems very wrong
    $('#server button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var server_id = $('#server_id').val();

        if (server_id) {
            var server = app.Servers.get(server_id);
        } else {
            var server = new app.Server();
        }

        server.save({
            name:         $('#server_name').val(),
            ip_address:   $('#server_address').val(),
            port:         $('#server_port').val(),
            user:         $('#server_user').val(),
            path:         $('#server_path').val(),
            deploy_code:  $('#server_deploy_code').is(':checked'),
            project_id:   parseInt($('input[name="project_id"]').val()),
            add_commands: $('#server_commands').is(':checked')
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!server_id) {
                    app.Servers.add(response);
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


    app.Server = Backbone.Model.extend({
        urlRoot: '/servers'
    });

    var Servers = Backbone.Collection.extend({
        model: app.Server,
        comparator: function(serverA, serverB) {
            if (serverA.get('name') > serverB.get('name')) {
                return -1; // before
            } else if (serverA.get('name') < serverB.get('name')) {
                return 1; // after
            }

            return 0; // equal
        }
    });

    app.Servers = new Servers();

    app.ServersTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#server_list tbody');

            $('#no_servers').show();
            $('#server_list').hide();

            this.listenTo(app.Servers, 'add', this.addOne);
            this.listenTo(app.Servers, 'reset', this.addAll);
            this.listenTo(app.Servers, 'remove', this.addAll);
            this.listenTo(app.Servers, 'all', this.render);

            app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
                var server = app.Servers.get(parseInt(data.model.id));

                if (server) {
                    server.set(data.model);
                }
            });

            app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
                if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
                    app.Servers.add(data.model);
                }
            });

            app.listener.on('server:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
                var server = app.Servers.get(parseInt(data.model.id));

                if (server) {
                    app.Servers.remove(server);
                }
            });
        },
        render: function () {
            if (app.Servers.length) {
                $('#no_servers').hide();
                $('#server_list').show();
            } else {
                $('#no_servers').show();
                $('#server_list').hide();
            }
        },
        addOne: function (server) {

            var view = new app.ServerView({
                model: server
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.Servers.each(this.addOne, this);
        }
    });

    app.ServerView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-test': 'testConnection',
            'click .btn-edit': 'editServer'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#server-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            data.status_css = 'primary';
            data.icon_css   = 'question';
            data.status     = Lang.get('servers.untested');

            if (parseInt(this.model.get('status')) === SUCCESSFUL) {
                data.status_css = 'success';
                data.icon_css   = 'check';
                data.status     = Lang.get('servers.successful');
            } else if (parseInt(this.model.get('status')) === TESTING) {
                data.status_css = 'warning';
                data.icon_css   = 'spinner fa-pulse';
                data.status     = Lang.get('servers.testing');
            } else if (parseInt(this.model.get('status')) === FAILED) {
                data.status_css = 'danger';
                data.icon_css   = 'warning';
                data.status     = Lang.get('servers.failed');
            }

            this.$el.html(this.template(data));

            return this;
        },
        editServer: function() {
            // FIXME: Sure this is wrong?
            $('#server_id').val(this.model.id);
            $('#server_name').val(this.model.get('name'));
            $('#server_address').val(this.model.get('ip_address'));
            $('#server_port').val(this.model.get('port'));
            $('#server_user').val(this.model.get('user'));
            $('#server_path').val(this.model.get('path'));

            $('#server_deploy_code').prop('checked', (this.model.get('deploy_code') === true));
        },
        testConnection: function() {
            if (parseInt(this.model.get('status')) === TESTING) {
                return;
            }

            this.model.set({
                status: TESTING
            });

            var that = this;
            $.ajax({
                type: 'POST',
                //url: '/projects/' + this.model.get('project_id') + this.model.urlRoot + '/' + this.model.id + '/test'
                url: this.model.urlRoot + '/' + this.model.id + '/test'
            }).fail(function (response) {
                that.model.set({
                    status: FAILED
                });
            });
        }
    });
})(jQuery);
