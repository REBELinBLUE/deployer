var app = app || {};

(function ($) {
    app.Command = Backbone.Model.extend({
        isBefore: function() {
            return (this.get('step').substring(0, 6) === 'Before');
        }
    });

    var Commands = Backbone.Collection.extend({
        model: app.Command
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
            // 'click .btn-test': 'testConnection',
            // 'click .btn-edit': 'editServer'
        },
        initialize: function () {
            // this.listenTo(this.model, 'change', this.render);
            // this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#command-template').html());
        },
        render: function () {
            // var data = this.model.toJSON();

            // data.status_css = 'primary';
            // data.icon_css = 'question';

            // if (this.model.get('status') === 'Successful') {
            //     data.status_css = 'success';
            //     data.icon_css = 'check';
            // } else if (this.model.get('status') === 'Testing') {
            //     data.status_css = 'warning';
            //     data.icon_css = 'spinner';
            // } else if (this.model.get('status') === 'Failed') {
            //     data.status_css = 'danger';
            //     data.icon_css = 'warning';
            // }

            this.$el.html(this.template(this.model.toJSON()));

            return this;
        }

    });
})(jQuery);