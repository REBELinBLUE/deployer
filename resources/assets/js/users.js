var app = app || {};

(function ($) {
   // FIXME: This seems very wrong
    $('#user').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.get('users.create');

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.existing-only', modal).hide();
        $('.new-only', modal).hide();
        $('.has-error', modal).removeClass('has-error');
        $('.label-danger', modal).remove();

        if (button.hasClass('btn-edit')) {
            title = Lang.get('users.edit');
            $('.btn-danger', modal).show();
            $('.existing-only', modal).show();
        } else {
            $('#user_id').val('');
            $('#user_name').val('');
            $('#user_email').val('');
            $('#user_password').val('');
            $('#user_password_confirmation').val('');

            $('.new-only', modal).show();
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#user button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var user = app.Users.get($('#user_id').val());

        user.destroy({
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
    $('#user button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var user_id = $('#user_id').val();

        if (user_id) {
            var user = app.Users.get(user_id);
        } else {
            var user = new app.User();
        }

        user.save({
            name:                  $('#user_name').val(),
            email:                 $('#user_email').val(),
            password:              $('#user_password').val(),
            password_confirmation: $('#user_password_confirmation').val()
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!user_id) {
                    app.Users.add(response);
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

    app.User = Backbone.Model.extend({
        urlRoot: '/admin/users',
        initialize: function() {

        }
    });

    var Users = Backbone.Collection.extend({
        model: app.User
    });

    app.Users = new Users();

    app.UsersTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#user_list tbody');

            this.listenTo(app.Users, 'add', this.addOne);
            this.listenTo(app.Users, 'reset', this.addAll);
            this.listenTo(app.Users, 'remove', this.addAll);
            this.listenTo(app.Users, 'all', this.render);

            app.listener.on('user:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
                var user = app.Users.get(parseInt(data.model.id));

                if (user) {
                    user.set(data.model);
                }
            });

            app.listener.on('user:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
                app.Users.add(data.model);
            });

            app.listener.on('user:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
                var user = app.Users.get(parseInt(data.model.id));

                if (user) {
                    app.Users.remove(user);
                }
            });
        },
        addOne: function (user) {
            var view = new app.UserView({
                model: user
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.Users.each(this.addOne, this);
        }
    });

    app.UserView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editUser'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#user-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            data.created = moment(data.created_at).format('Do MMMM YYYY h:mm:ss A');

            this.$el.html(this.template(data));

            return this;
        },
        editUser: function() {
            $('#user_id').val(this.model.id);
            $('#user_name').val(this.model.get('name'));
            $('#user_email').val(this.model.get('email'));
        }
    });
})(jQuery);
