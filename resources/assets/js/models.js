var app = app || {};

(function ($) {

    app.Server = Backbone.Model.extend({
        defaults: {
        }
    });

    var Servers = Backbone.Collection.extend({
        url: '/projects/1/servers',
        model: app.Server,
        comparator: function(serverA, serverB) {
            if (serverA.get('name') > serverB.get('name')) {
                return -1; // before
            } else if (serverA.get('name') > serverB.get('name')) {
                return 1; // after
            }

            return 0; // equal
        }
    });

    app.Servers = new Servers();

    app.AppView = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {

            this.$list = $('#server_list tbody');

            this.listenTo(app.Servers, 'add', this.addOne);
            this.listenTo(app.Servers, 'reset', this.addAll);
            this.listenTo(app.Servers, 'all', this.render);

            app.Servers.fetch({
                reset: true
            });

        },
        render: function () {
            if (app.Servers.length) {
                $('#no_servers').hide();
                $('#server_list').show();
            } else {
                $('#no_servers').show();
                $('#server_list').hide();
            }
        },
        addOne: function (server) {
            var view = new app.ServerView({ model: server });
            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.Servers.each(this.addOne, this);
        }
    });

    app.ServerView = Backbone.View.extend({
        tagName:  'tr',
        template: _.template($('#server-template').html()),
        events: {
            'click .btn-test': 'testConnection',
            'click .btn-edit': 'editServer'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },
        render: function () {
            this.$el.html(this.template(this.model.toJSON()));

            var $refresh = $('i.fa-refresh', this.$el);

            var status_css = 'primary';
            var icon_css = 'question';

            if (this.model.get('status') === 'Successful') {
                status_css = 'success';
                icon_css = 'check';
            } else if (this.model.get('status') === 'Testing') {
                status_css = 'warning';
                icon_css = 'spinner';

                $refresh.addClass('fa-spin');
            } else if (this.model.get('status') === 'Failed') {
                status_css = 'danger';
                icon_css = 'warning';
            }

            this.$el.find('td>span').removeClass().addClass('label label-' + status_css);
            this.$el.find('td>span>i').removeClass().addClass('fa fa-' + icon_css);

            return this;
        },
        editServer: function() {
            console.log('edit');
            console.log(this.model);
        },
        callback: false,
        checkServer: function() {


            console.log(this.model.id);


            // var cb = function() {

            //     $.ajax({
            //         type: 'GET',
            //         url: '/servers/' + that.model.id,
            //     }).done(function (data) {
            //         if (data.status != 'Testing') {
            //             clearInterval(that.callback);

            //             that.model.set({
            //                 status: data.status
            //             });
            //         }
            //     });
            // });

            // this.callback = setInterval(cb, 2500);
        },
        testConnection: function() {
            if (this.model.attributes.status === 'Testing') {
                return;
            }

            this.model.set({
                status: 'Testing'
            });

            var that = this;

            this.checkServer();

            $.ajax({
                type: 'GET',
                url: '/servers/' + this.model.id + '/test'
            }).fail(function (response) {
                that.model.set({
                    status: 'Failed'
                });
            });
        }
    });
})(jQuery);