var app = app || {};

(function ($) {
    var COMPLETED = 0;
    var PENDING   = 1;
    var RUNNING   = 2;
    var FAILED    = 3;
    var CANCELLED = 4;

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
            url: '/log/' + log_id
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
        urlRoot: '/status',
        poller: false,
        initialize: function() {
            this.on('change:status', this.changeStatus, this);
            
            this.changeStatus();
        },
        changeStatus: function() {
            // Start polling the model if it is running, or it is the first model in the collection as is pending
            var poll_for_update = false;
            if (parseInt(this.get('status')) === PENDING) {
                if (this.get('first') === true) {
                    poll_for_update = true;
                }
            } else if (parseInt(this.get('status')) === RUNNING) {
                poll_for_update = true;
            }

            if (poll_for_update) {
                isChecking = true;

                var that = this;

                $.ajax({
                    type: 'GET',
                    url: this.urlRoot + '/' + this.id
                }).fail(function (response) {
                    that.set({
                        status: FAILED
                    });
                }).success(function () {
                    that.poller = Backbone.Poller.get(that, {
                        condition: function(model) {
                            var stillRunning = (parseInt(model.get('status')) === RUNNING);

                            if (parseInt(model.get('status')) === PENDING && model.get('first') === true) {
                                stillRunning = true;
                            }

                            if (parseInt(model.get('status')) === COMPLETED) {
                                var found = _.find(app.Deployment.models, function(next) { 
                                    return parseInt(next.get('status')) === PENDING;
                                });

                                if (found) {
                                    found.set({
                                        status: RUNNING
                                    });
                                }

                                return false;
                            }

                            isChecking = stillRunning;

                            return stillRunning;
                        },
                        delay: 1000
                    });

                    that.poller.start();
                });
            }
            else if (parseInt(this.get('status')) === FAILED || parseInt(this.get('status')) === CANCELLED) {
                _.each(app.Deployment.models, function(remaining) {
                    if (parseInt(remaining.get('status')) === PENDING) {
                        remaining.set({
                            status: CANCELLED
                        });
                    }
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
                    step: parseInt($(element).attr('id').replace('step_', '')),
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
                return parseInt(element.step) === parseInt(step.get('deploy_step_id'));
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
            data.status = Lang.status.pending;

            if (parseInt(this.model.get('status')) === COMPLETED) {
                data.status_css = 'success';
                data.icon_css = 'check';
                data.status = Lang.status.completed;
            } else if (parseInt(this.model.get('status')) === RUNNING) {
                data.status_css = 'warning';
                data.icon_css = 'spinner fa-spin';
                data.status = Lang.status.running;
            } else if (parseInt(this.model.get('status')) === FAILED || parseInt(this.model.get('status')) === CANCELLED) {
                data.status_css = 'danger';
                data.icon_css = 'warning';

                data.status = Lang.status.failed;
                if (parseInt(this.model.get('status')) === CANCELLED) {
                    data.status = Lang.status.cancelled;
                }
            }

            data.formatted_start_time = data.started_at ? moment(data.started_at).format('h:mm:ss A') : false;
            data.formatted_end_time   = data.finished_at ? moment(data.finished_at).format('h:mm:ss A') : false;
            data.total_time           = data.runtime !== null ? data.runtime : false;

            this.$el.html(this.template(data));

            return this;
        }
    });
})(jQuery);