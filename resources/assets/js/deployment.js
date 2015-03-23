var app = app || {};

(function ($) {
    app.ServerLog = Backbone.Model.extend({
        urlRoot: '/status',
        poller: false,
        initialize: function() {
            this.on('change:status', this.changeStatus, this);

            var that = this;

            $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                jqXHR.setRequestHeader('X-CSRF-Token', $('meta[name="token"]').attr('content'));
            });

            this.changeStatus();
        },
        changeStatus: function() {
            if (this.get('status') === 'Running') {
                var that = this;

                $.ajax({
                    type: 'GET',
                    url: this.urlRoot + '/' + this.id
                }).fail(function (response) {
                    that.set({
                        status: 'Failed'
                    });
                }).success(function () {
                    that.poller = Backbone.Poller.get(that, {
                        condition: function(model) {
                            var stillRunning = (model.get('status') === 'Running');

                            if (model.get('status') === 'Completed') {
                                var next = _.find(app.Deployment, function(element) {
                                    return model.get('id') + 1 === element.get('id');
                                });

                                next.set({
                                    status: 'Running'
                                });

                                return false;
                            }

                            return stillRunning;
                        },
                        delay: 2500
                    });

                    that.poller.start();
                });
            }
        }
    });

    var Deployment = Backbone.Collection.extend({
        model: app.ServerLog
    });

    app.Deployment = new Deployment();

    app.DeploymentView = Backbone.View.extend({
        el: '#app',
        $containers: [],
        events: {

        },
        initialize: function() {
            var that = this;
            $('.deploy-step tbody').each(function(index, element) {
                that.$containers.push({
                    step: $(element).attr('id').replace('step_', ''),
                    element: element
                })
            });

            this.listenTo(app.Deployment, 'add', this.addOne);
            this.listenTo(app.Deployment, 'reset', this.addAll);
            this.listenTo(app.Deployment, 'all', this.render);
        },
        addOne: function (step) {
            var view = new app.LogView({ 
                model: step
            });

            var found = _.find(this.$containers, function(element) { 
                return element.step === step.get('deploy_step_id');
            });

            $(found.element).append(view.render().el);

        },
        addAll: function () {
            $(this.$containers).each(function (index, element) {
                element.html('');
            });

            app.Commands.each(this.addOne, this);
        }
    });
    
    app.LogView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            //'click .btn-edit': 'editCommand'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#log-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            data.status_css = 'info';
            data.icon_css = 'clock-o';

            if (this.model.get('status') === 'Completed') {
                data.status_css = 'success';
                data.icon_css = 'check';
            } else if (this.model.get('status') === 'Running') {
                data.status_css = 'warning';
                data.icon_css = 'spinner fa-spin';
            } else if (this.model.get('status') === 'Failed' || this.model.get('status') === 'Cancelled') {
                data.status_css = 'danger';
                data.icon_css = 'warning';
            }

            data.start_time = 'N/A';
            data.end_time = 'N/A';
            data.total_time = 'N/A';

            if (data.started !== null) {
                data.start_time = data.started;
            }

            if (data.finished !== null) {
                data.end_time = data.finished;
            }

            if (data.runtime !== null) {
                data.total_time = data.runtime;
            }

            this.$el.html(this.template(data));

            return this;
        }
    });
})(jQuery);