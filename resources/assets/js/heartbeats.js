var app = app || {};

(function ($) {
    var OK       = 0;
    var UNTESTED = 1;
    var MISSING  = 2;

    // FIXME: This seems very wrong
    $('#heartbeat').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.heartbeats.create;

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        if (button.hasClass('btn-edit')) {
            title = Lang.heartbeats.edit;
            $('.btn-danger', modal).show();
        } else {
            $('#heartbeat_id').val('');
            $('#heartbeat_name').val('');
            $('#heartbeat_interval_30').prop('checked', true);
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#heartbeat button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var heartbeat = app.Heartbeats.get($('#heartbeat_id').val());

        heartbeat.destroy({
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                app.Heartbeats.remove(heartbeat);
            },
            error: function() {
                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        });
    });

    // FIXME: This seems very wrong
    $('#heartbeat button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var heartbeat_id = $('#heartbeat_id').val();

        if (heartbeat_id) {
            var heartbeat = app.Heartbeats.get(heartbeat_id);
        } else {
            var heartbeat = new app.Heartbeat();
        }

        heartbeat.save({
            name:        $('#heartbeat_name').val(),
            interval:    parseInt($('input[name=interval]:checked').val()),
            project_id:  $('input[name="project_id"]').val()
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!heartbeat_id) {
                    app.Heartbeats.add(response);
                }
            },
            error: function(model, response, options) {
                $('.callout-danger', dialog).show();

                var errors = response.responseJSON;

                $('form input', dialog).each(function (index, element) {
                    element = $(element);

                    var name = element.attr('name');

                    if (typeof errors[name] !== 'undefined') {
                        element.parent('div').addClass('has-error');
                    }
                });

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        });
    });

    app.Heartbeat = Backbone.Model.extend({
        urlRoot: '/heartbeats',
        poller: false
    });

    var Heartbeats = Backbone.Collection.extend({
        model: app.Heartbeat,
        comparator: function(heartbeatA, heartbeatB) {
            if (heartbeatA.get('name') > heartbeatB.get('name')) {
                return -1; // before
            } else if (heartbeatA.get('name') < heartbeatB.get('name')) {
                return 1; // after
            }

            return 0; // equal
        }
    });

    app.Heartbeats = new Heartbeats();

    app.HeartbeatsTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#heartbeat_list tbody');

            $('#no_heartbeats').show();
            $('#heartbeat_list').hide();

            this.listenTo(app.Heartbeats, 'add', this.addOne);
            this.listenTo(app.Heartbeats, 'reset', this.addAll);
            this.listenTo(app.Heartbeats, 'all', this.render);
        },
        render: function () {
            if (app.Heartbeats.length) {
                $('#no_heartbeats').hide();
                $('#heartbeat_list').show();
            } else {
                $('#no_heartbeats').show();
                $('#heartbeat_list').hide();
            }
        },
        addOne: function (heartbeat) {

            var view = new app.HeartbeatView({ 
                model: heartbeat
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.Heartbeats.each(this.addOne, this);
        }
    });

    app.HeartbeatView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editHeartbeat'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#heartbeat-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            data.status_css = 'primary';
            data.icon_css   = 'question';
            data.status     = Lang.heartbeats.status.untested;
            data.has_run    = false;

            if (parseInt(this.model.get('status')) === OK) {
                data.status_css = 'success';
                data.icon_css   = 'check';
                data.status     = Lang.heartbeats.status.ok;
                data.has_run    = true;
            } else if (parseInt(this.model.get('status')) === MISSING) {
                data.status_css = 'danger';
                data.icon_css   = 'warning';
                data.status     = Lang.heartbeats.status.missing;
                data.has_run    = data.last_activity ? true : false;
            }

            data.interval_label = Lang.heartbeats.intervals[data.interval];

            data.formatted_date = '';
            if (data.has_run) {
                data.formatted_date = moment(data.last_activity).format('Do MMM YYYY h:mm:ss A');
            }

            this.$el.html(this.template(data));

            return this;
        },
        editHeartbeat: function() {
            // FIXME: Sure this is wrong?
            $('#heartbeat_id').val(this.model.id);
            $('#heartbeat_name').val(this.model.get('name'));
            $('#heartbeat_interval_' + this.model.get('interval')).prop('checked', true);
        }
    });
})(jQuery);