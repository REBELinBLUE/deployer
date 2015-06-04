var app = app || {};

(function ($) {
   // FIXME: This seems very wrong
    $('#template').on('show.bs.modal', function (event) {
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
            $('#template_id').val('');
            $('#template_name').val('');
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#template button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var template = app.Templates.get($('#template_id').val());

        template.destroy({
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                app.Templates.remove(template);
            },
            error: function() {
                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        })
    });

    // FIXME: This seems very wrong
    $('#template button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var template_id = $('#template_id').val();

        if (template_id) {
            var template = app.Templates.get(template_id);
        } else {
            var template = new app.Template();
        }

        template.save({
            name: $('#template_name').val()
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!template_id) {
                    app.Templates.add(response);

                    window.location.href = '/admin/templates/' + response.id + '/commands';
                }
            },
            error: function(model, response, options) {
                $('.callout-danger', dialog).show();

                var errors = response.responseJSON;

                $('form input', dialog).each(function (index, element) {
                    element = $(element);

                    var name = element.attr('name');

                    if (typeof errors[name] != 'undefined') {
                        element.parent('div').addClass('has-error');
                    }
                });

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        });
    });

    app.Template = Backbone.Model.extend({
        urlRoot: '/admin/templates',
        poller: false,
        initialize: function() {

        }
    });

    var Templates = Backbone.Collection.extend({
        model: app.Template
    });

    app.Templates = new Templates();

    app.TemplatesTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {

            $('#template_list').hide();
            $('#no_templates').show();

            this.$list = $('#template_list tbody');

            this.listenTo(app.Templates, 'add', this.addOne);
            this.listenTo(app.Templates, 'reset', this.addAll);
            this.listenTo(app.Templates, 'all', this.render);
        },
        render: function () {
            if (app.Templates.length) {
                $('#no_templates').hide();
                $('#template_list').show();
            } else {
                $('#no_templates').show();
                $('#template_list').hide();
            }
        },
        addOne: function (template) {

            var view = new app.TemplateView({ 
                model: template
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.Servers.each(this.addOne, this);
        }
    });

    app.TemplateView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editTemplate'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            var source = $('#template-template').html();
            source = source.replace('var_template_id', '<%- id %>');

            this.template = _.template(source);
        },
        render: function () {
            var data = this.model.toJSON();

            this.$el.html(this.template(data));

            return this;
        },
        editTemplate: function() {
            $('#template_id').val(this.model.id);
            $('#template_name').val(this.model.get('name'));
        }
    });
})(jQuery);