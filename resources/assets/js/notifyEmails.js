var app = app || {};

(function ($) {
    // FIXME: This seems very wrong
    $('#notifyemail').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.notifyEmails.create;

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        if (button.hasClass('btn-edit')) {
            title = Lang.notifyEmails.edit;
            $('.btn-danger', modal).show();
        } else {
            $('#notifyemail_id').val('');
            $('#notifyemail_name').val('');
            $('#notifyemail_address').val('');
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#notifyemail button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var file = app.NotifyEmails.get($('#notifyemail_id').val());

        file.destroy({
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                app.NotifyEmails.remove(file);
            },
            error: function() {
                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        });
    });

    // FIXME: This seems very wrong
    $('#notifyemail button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var notifyemail_id = $('#notifyemail_id').val();

        if (notifyemail_id) {
            var file = app.NotifyEmails.get(notifyemail_id);
        } else {
            var file = new app.NotifyEmail();
        }

        file.save({
            name:       $('#notifyemail_name').val(),
            email:      $('#notifyemail_address').val(),
            project_id: $('input[name="project_id"]').val()
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!notifyemail_id) {
                    app.NotifyEmails.add(response);
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

    app.NotifyEmail = Backbone.Model.extend({
        urlRoot: '/notify-email',
        poller: false
    });

    var NotifyEmails = Backbone.Collection.extend({
        model: app.NotifyEmail
    });

    app.NotifyEmails = new NotifyEmails();

    app.NotifyEmailsTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#notifyemail_list tbody');

            $('#no_notifyemails').show();
            $('#notifyemail_list').hide();

            this.listenTo(app.NotifyEmails, 'add', this.addOne);
            this.listenTo(app.NotifyEmails, 'reset', this.addAll);
            this.listenTo(app.NotifyEmails, 'all', this.render);
        },
        render: function () {
            if (app.NotifyEmails.length) {
                $('#no_notifyemails').hide();
                $('#notifyemail_list').show();
            } else {
                $('#no_notifyemails').show();
                $('#notifyemail_list').hide();
            }
        },
        addOne: function (file) {

            var view = new app.EmailView({ 
                model: file
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.NotifyEmails.each(this.addOne, this);
        }
    });

    app.EmailView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editEmail'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#notifyemail-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            this.$el.html(this.template(data));

            return this;
        },
        editEmail: function() {
            // FIXME: Sure this is wrong?
            $('#notifyemail_id').val(this.model.id);
            $('#notifyemail_name').val(this.model.get('name'));
            $('#notifyemail_address').val(this.model.get('email'));
        }
    });

})(jQuery);