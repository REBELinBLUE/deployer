var app = app || {};

(function ($) {
    // FIXME: This seems very wrong
    $('#notification').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.callout-warning', modal).hide();
        $('.has-error', modal).removeClass('has-error');
        $('.label-danger', modal).remove();

        if (button.hasClass('btn-edit')) {
            $('.btn-danger', modal).show();
        } else {
            $('#notification_id').val('');
            $('#notification_name').val('');
            $('#notification_type').val('');
            $('#notification :input[id^=notification_config]').val('');
            $('#notification .channel-config input[type=checkbox]').prop('checked', true);
            $('#notification .modal-footer').hide();
            $('.channel-config').hide();
            $('#channel-type').show();
            modal.find('.modal-title span').text(Lang.get('channels.create'));
        }
    });

    $('#notification #channel-type a.btn-app').on('click', function(event) {
        var button = $(event.currentTarget);
        var modal = $('#notification');

        if (button.attr('disabled')) {
            $('.callout-warning', modal).show();
            return;
        }

        $('.callout-warning', modal).hide();

        var type = button.data('type');
        setTitleWithIcon(type, 'create');
    });

    function setTitleWithIcon(type, action) {
        $('#notification .modal-title span').text(Lang.get('channels.' + action + '_' + type));

        var element = $('#notification .modal-title i').removeClass().addClass('fa');
        var icon = 'cogs';

        if (type === 'slack') {
            icon = 'slack';
        } else if (type === 'hipchat') {
            element.addClass('fa-flip-horizontal');
            icon = 'comment-o';
        } else if (type === 'mail') {
            icon = 'envelope-o';
        } else if (type === 'twilio') {
            icon = 'mobile';
        }

        element.addClass('fa-' + icon);

        $('#notification .modal-footer').show();
        $('.channel-config').hide();
        $('#channel-type').hide();
        $('#channel-name').show();
        $('#channel-triggers').show();
        $('#channel-config-' + type).show();
        $('#notification_type').val(type);
    }

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

        var data = {
          config:                     null,
          name:                       $('#notification_name').val(),
          type:                       $('#notification_type').val(),
          project_id:                 parseInt($('input[name="project_id"]').val()),
          on_deployment_success:      $('#notification_on_deployment_success').is(':checked'),
          on_deployment_failure:      $('#notification_on_deployment_failure').is(':checked'),
          on_link_down:               $('#notification_on_link_down').is(':checked'),
          on_link_still_down:         $('#notification_on_link_still_down').is(':checked'),
          on_link_recovered:          $('#notification_on_link_recovered').is(':checked'),
          on_heartbeat_missing:       $('#notification_on_heartbeat_missing').is(':checked'),
          on_heartbeat_still_missing: $('#notification_on_heartbeat_still_missing').is(':checked'),
          on_heartbeat_recovered:     $('#notification_on_heartbeat_recovered').is(':checked')
        };

        $('#notification #channel-config-' + data.type + ' :input[id^=notification_config]').each(function(key, field) {
            var name = $(field).attr('name');

            data[name] = $(field).val();
        });

        notification.save(data, {
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


            app.listener.on('channel:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
                var notification = app.Notifications.get(parseInt(data.model.id));

                if (server) {
                    notification.set(data.model);
                }
            });

            app.listener.on('channel:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
                if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
                    app.Notifications.add(data.model);
                }
            });

            app.listener.on('channel:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
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

            data.icon = 'cogs';
            data.label = Lang.get('channels.custom');

            if (this.model.get('type') !== 'custom') {
                data.label = Lang.get('channels.' + this.model.get('type'));
            }

            if (this.model.get('type') === 'slack') {
                data.icon = 'slack';
            } else if (this.model.get('type') === 'hipchat') {
                data.icon = 'comment-o fa-flip-horizontal';
            } else if (this.model.get('type') === 'mail') {
                data.icon = 'envelope-o';
            } else if (this.model.get('type') === 'twilio') {
                data.icon = 'mobile';
            }

            this.$el.html(this.template(data));

            return this;
        },
        editNotification: function() {
            var type = this.model.get('type');

            $.each(this.model.get('config'), function(field, value) {
                $('#channel-config-' + type + ' #notification_config_' + field).val(value);
            });

            // FIXME: Sure this is wrong?
            $('#notification_id').val(this.model.id);
            $('#notification_name').val(this.model.get('name'));
            $('#notification_type').val(type);
            $('#notification_on_deployment_success').prop('checked', (this.model.get('on_deployment_success') === true));
            $('#notification_on_deployment_failure').prop('checked', (this.model.get('on_deployment_failure') === true));
            $('#notification_on_link_down').prop('checked', (this.model.get('on_link_down') === true));
            $('#notification_on_link_still_down').prop('checked', (this.model.get('on_link_still_down') === true));
            $('#notification_on_link_recovered').prop('checked', (this.model.get('on_link_recovered') === true));
            $('#notification_on_heartbeat_missing').prop('checked', (this.model.get('on_heartbeat_missing') === true));
            $('#notification_on_heartbeat_still_missing').prop('checked', (this.model.get('on_heartbeat_still_missing') === true));
            $('#notification_on_heartbeat_recovered').prop('checked', (this.model.get('on_heartbeat_recovered') === true));

            setTitleWithIcon(this.model.get('type'), 'edit');
        }
    });
})(jQuery);
