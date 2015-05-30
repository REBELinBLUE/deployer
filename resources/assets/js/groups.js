var app = app || {};

(function ($) {
   // FIXME: This seems very wrong
    $('#group').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.create;

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        if (button.hasClass('btn-edit')) {
            title = Lang.edit;
            $('.btn-danger', modal).show();
        } else {
            $('#group_id').val('');
            $('#group_name').val('');
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#group button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var group_id = $('#group_id').val();

        if (group_id) {
            var group = app.Groups.get(group_id);
        } else {
            var group = new app.Group();
        }

        group.save({
            name: $('#group_name').val()
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!group_id) {
                    app.Groups.add(response);
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

    app.Group = Backbone.Model.extend({
        urlRoot: '/admin/groups',
        poller: false,
        initialize: function() {

        }
    });

    var Groups = Backbone.Collection.extend({
        model: app.Group
    });

    app.Groups = new Groups();

    app.GroupsTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#group_list tbody');

            this.listenTo(app.Groups, 'add', this.addOne);
            this.listenTo(app.Groups, 'reset', this.addAll);
            this.listenTo(app.Groups, 'all', this.render);
        },
        addOne: function (group) {

            var view = new app.GroupView({ 
                model: group
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.Servers.each(this.addOne, this);
        }
    });

    app.GroupView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editGroup'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#group-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            this.$el.html(this.template(data));

            return this;
        },
        editGroup: function() {
            $('#group_id').val(this.model.id);
            $('#group_name').val(this.model.get('name'));
        }
    });
})(jQuery);