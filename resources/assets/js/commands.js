var app = app || {};

(function ($) {
    // FIXME: This seems very wrong
    $('#command').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = 'Add command';

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        if (button.hasClass('btn-edit')) {
            title = 'Edit Command';
            $('.btn-danger', modal).show();
        } else {
            $('#command_id').val('');
            $('#command_step').val(button.data('step'));
            $('#command_name').val('');
            $('#command_script').val('');
            $('#command_user').val('');

            $('.command-server').prop('checked', true);
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#command button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var command = app.Commands.get($('#command_id').val());

        command.destroy({
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                app.Commands.remove(command);
            },
            error: function() {
                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        })
    });

    // FIXME: This seems very wrong
    $('#command button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find(':input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var command_id = $('#command_id').val();

        if (command_id) {
            var command = app.Commands.get(command_id);
        } else {
            var command = new app.Command();
        }

        var server_ids = [];

        $('.command-server:checked').each(function() {
            server_ids.push($(this).val());
        });

        command.save({
            name:       $('#command_name').val(),
            script:     $('#command_script').val(),
            user:       $('#command_user').val(),
            step:       $('#command_step').val(),
            project_id: $('input[name="project_id"]').val(),
            servers:    server_ids
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find(':input').removeAttr('disabled');

                if (!command_id) {
                    app.Commands.add(response);
                }
            },
            error: function(model, response, options) {
                $('.callout-danger', dialog).show();

                var errors = response.responseJSON.errors;

                $('form input', dialog).each(function (index, element) {
                    element = $(element);

                    var name = element.attr('name');

                    if (typeof errors[name] != 'undefined') {
                        element.parent('div').addClass('has-error');
                    }
                });

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find(':input').removeAttr('disabled');
            }
        });
    });



    app.Command = Backbone.Model.extend({
        urlRoot: '/commands',
        isBefore: function() {
            return (this.get('step').substring(0, 6) === 'Before');
        },
        initialize: function() {
            var that = this;

            $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                jqXHR.setRequestHeader('X-CSRF-Token', $('meta[name="token"]').attr('content'));
            });
        }
    });

    var Commands = Backbone.Collection.extend({
        model: app.Command,
        comparator: function(modelA, modelB) {
            var a = modelA.get('order');
            var b = modelB.get('order');

            if (a > b) {
                return 1;
            }
            else if (a < b) {
                return -1;
            }

            return 0;
        }
    });

    app.Commands = new Commands();

    app.CommandsTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$beforeList = $('#commands-before .command-list tbody');
            this.$afterList = $('#commands-after .command-list tbody');

            this.listenTo(app.Commands, 'add', this.addOne);
            this.listenTo(app.Commands, 'reset', this.addAll);
            this.listenTo(app.Commands, 'all', this.render);
        },
        render: function () {
            var before = app.Commands.find(function(model) { 
                return model.isBefore();
            });

            if (typeof before != 'undefined') {
                $('#commands-before .no-commands').hide();
                $('#commands-before .commandslist').show();
            } else {
                $('#commands-before .no-commands').show();
                $('#commands-before .command-list').hide();
            }

            var after = app.Commands.find(function(model) { 
                return !model.isBefore();
            });

            if (typeof after != 'undefined') {
                $('#commands-after .no-commands').hide();
                $('#commands-after .command-list').show();
            } else {
                $('#commands-after .no-commands').show();
                $('#commands-after .command-list').hide();
            }
        },
        addOne: function (command) {
            var view = new app.CommandView({ 
                model: command
            });

            if (command.isBefore()) {
                this.$beforeList.append(view.render().el);
            } else {
                this.$afterList.append(view.render().el);
            }

        },
        addAll: function () {
            this.$beforeList.html('');
            this.$afterList.html('');
            app.Commands.each(this.addOne, this);
        }
    });

    app.CommandView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editCommand'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#command-template').html());
        },
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));

            return this;
        },
        editCommand: function() {
            // FIXME: Sure this is wrong?
            $('#command_id').val(this.model.id);
            $('#command_step').val(this.model.get('step'));
            $('#command_name').val(this.model.get('name'));
            $('#command_script').val(this.model.get('script'));
            $('#command_user').val(this.model.get('user'));

            $('.command-server').prop('checked', false);
            $(this.model.get('servers')).each(function (index, server) {
                $('#command_server_' + server.id).prop('checked', true);
            });
        }
    });
})(jQuery);