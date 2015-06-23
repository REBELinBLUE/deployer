var app = app || {};

(function ($) {
    // FIXME: This seems very wrong
    $('#notification').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.notifications.create;

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');
        $('.label-danger', modal).remove();

        if (button.hasClass('btn-edit')) {
            title = Lang.notifications.edit;
            $('.btn-danger', modal).show();
        } else {
            $('#notification_id').val('');
            $('#notification_name').val('');
            $('#notification_webhook').val('');
            $('#notification_channel').val('');
            $('#notification_icon').val('');
            $('#notification_failure_only').prop('checked', true);
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#notification button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var notification = app.Notifications.get($('#notification_id').val());

        notification.destroy({
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
    $('#notification button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var notification_id = $('#notification_id').val();

        if (notification_id) {
            var notification = app.Notifications.get(notification_id);
        } else {
            var notification = new app.Notification();
        }

        notification.save({
            name:         $('#notification_name').val(),
            webhook:      $('#notification_webhook').val(),
            channel:      $('#notification_channel').val(),
            icon:         $('#notification_icon').val(),
            project_id:   $('input[name="project_id"]').val(),
            failure_only: $('#notification_failure_only').is(':checked')
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!notification_id) {
                    app.Notifications.add(response);
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
                        var parent = element.parent('div');
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



    app.Notification = Backbone.Model.extend({
        urlRoot: '/notifications'
    });

    var Notifications = Backbone.Collection.extend({
        model: app.Notification
    });

    app.Notifications = new Notifications();

    app.NotificationsTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#notification_list tbody');

            $('#no_notifications').show();
            $('#notification_list').hide();

            this.listenTo(app.Notifications, 'add', this.addOne);
            this.listenTo(app.Notifications, 'reset', this.addAll);
            this.listenTo(app.Notifications, 'remove', this.addAll);
            this.listenTo(app.Notifications, 'all', this.render);


            app.listener.on('notification:App\\Events\\ModelChanged', function (data) {
                var notification = app.Notifications.get(parseInt(data.model.id));

                if (server) {
                    notification.set(data.model);
                }
            });

            app.listener.on('notification:App\\Events\\ModelCreated', function (data) {
                if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
                    app.Notifications.add(data.model);
                }
            });

            app.listener.on('notification:App\\Events\\ModelTrashed', function (data) {
                var notification = app.Notifications.get(parseInt(data.model.id));

                if (notification) {
                    app.Notifications.remove(notification);
                }
            });
        },
        render: function () {
            if (app.Notifications.length) {
                $('#no_notifications').hide();
                $('#notification_list').show();
            } else {
                $('#no_notifications').show();
                $('#notification_list').hide();
            }
        },
        addOne: function (notification) {

            var view = new app.NotificationView({ 
                model: notification
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.Notifications.each(this.addOne, this);
        }
    });

    app.NotificationView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editNotification'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#notification-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            this.$el.html(this.template(data));

            return this;
        },
        editNotification: function() {
            // FIXME: Sure this is wrong?
            $('#notification_id').val(this.model.id);
            $('#notification_name').val(this.model.get('name'));
            $('#notification_webhook').val(this.model.get('webhook'));
            $('#notification_channel').val(this.model.get('channel'));
            $('#notification_icon').val(this.model.get('icon'));
            $('#notification_failure_only').prop('checked', (this.model.get('failure_only') === true));
        }
    });
})(jQuery);