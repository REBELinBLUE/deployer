var app = app || {};

(function ($) {

    app.Template = Backbone.Model.extend({
        urlRoot: '/admin/templates'
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
            this.$list = $('#template_list tbody');

            $('#template_list').hide();
            $('#no_templates').show();

            this.listenTo(app.Templates, 'add', this.addOne);
            this.listenTo(app.Templates, 'reset', this.addAll);
            this.listenTo(app.Templates, 'remove', this.addAll);
            this.listenTo(app.Templates, 'all', this.render);

            app.listener.on('template:App\\Events\\ModelChanged', function (data) {
                var template = app.Templates.get(parseInt(data.model.id));

                if (template) {
                    template.set(data.model);
                }
            });

            app.listener.on('template:App\\Events\\ModelCreated', function (data) {
                app.Templates.add(data.model);
            });

            app.listener.on('template:App\\Events\\ModelTrashed', function (data) {
                var template = app.Templates.get(parseInt(data.model.id));

                if (template) {
                    app.Templates.remove(template);
                }
            });
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
            app.Templates.each(this.addOne, this);
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

            this.template = _.template($('#template-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            this.$el.html(this.template(data));

            return this;
        },
        editTemplate: function() {
            /*
            $('#project_id').val(this.model.id);
            $('#project_name').val(this.model.get('name'));
            $('#project_repository').val(this.model.get('repository'));
            $('#project_branch').val(this.model.get('branch'));
            $('#project_group_id').val(this.model.get('group_id'));
            $('#project_builds_to_keep').val(this.model.get('builds_to_keep'));
            $('#project_url').val(this.model.get('url'));
            $('#project_build_url').val(this.model.get('build_url'));
            */
        }
    });
})(jQuery);