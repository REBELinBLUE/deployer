var app = app || {};

(function ($) {

    $('#log').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var log_id = button.attr('id').replace('log_', '');

        var step = $('h3 span', button.parents('.box')).text();
        var modal = $(this);
        var log = $('pre', modal);
        var loader = $('#loading', modal);

        log.hide();
        loader.show();

        $('#action', modal).text(step);
        log.text('');

        $.ajax({
            type: 'GET',
            url: '/logs/' + log_id
        }).done(function (data) {
            var output = data.output;
            // FIXME: There has to be a cleaner way to do this surely?
            output = output.replace(/<\/error>/g, '</span>')
            output = output.replace(/<\/info>/g, '</span>');
            output = output.replace(/<error>/g, '<span class="text-red">')
            output = output.replace(/<info>/g, '<span class="text-default">');

            log.html(output);

            log.show();
            loader.hide();
        }).fail(function() {

        }).always(function() {

        });
    });

    var isChecking = false;

    app.ServerLog = Backbone.Model.extend({
        urlRoot: '/logs',
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
                isChecking = true;

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
                                var found = _.find(app.Deployment.models, function(next) { 
                                    return next.get('status') === 'Pending';
                                });

                                if (found) {
                                    found.set({
                                        status: 'Running'
                                    });
                                }

                                return false;
                            } else if (model.get('status') === 'Failed') {
                                console.log('failed');

                                _.each(app.Deployment.models, function(remaining) {
                                    if (remaining.get('status') === 'Pending') {
                                        remaining.set({
                                            status: 'Cancelled'
                                        });
                                    }
                                });
                            }

                            isChecking = stillRunning;

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
            //'click .btn-log': 'showLog',
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