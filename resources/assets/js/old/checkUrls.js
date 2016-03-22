var app = app || {};

(function ($) {
    var SUCCESS = 0;
    var FAILED = 1;

    $('#checkurl').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.get('checkUrls.create');

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');
        $('.label-danger', modal).remove();

        if (button.hasClass('btn-edit')) {
            title = Lang.get('checkUrls.edit');
            $('.btn-danger', modal).show();
        } else {
            $('#url_id').val('');
            $('#title').val('');
            $('#url').val('');
            $('#period_5').prop('checked', true);
            //$('#is_report').prop('checked', false);
        }

        modal.find('.modal-title span').text(title);
    });

    $('#checkurl button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var url = app.CheckUrls.get($('#url_id').val());

        url.destroy({
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

    $('#checkurl button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var url_id = $('#url_id').val();

        if (url_id) {
            var url = app.CheckUrls.get(url_id);
        } else {
            var url = new app.CheckUrl();
        }

        url.save({
            title:      $('#title').val(),
            url:        $('#url').val(),
            is_report:  true, // $('#is_report').prop('checked'),
            period:     parseInt($('input[name=period]:checked').val()),
            project_id: $('input[name="project_id"]').val()
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!url_id) {
                    app.CheckUrls.add(response);
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
                        var parent = element.parent('div');
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

    app.CheckUrl = Backbone.Model.extend({
        urlRoot: '/check-url'
    });

    var CheckUrls = Backbone.Collection.extend({
        model: app.CheckUrl
    });

    app.CheckUrls = new CheckUrls();

    app.CheckUrlsTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#checkurl_list tbody');

            $('#no_checkurls').show();
            $('#checkurl_list').hide();

            this.listenTo(app.CheckUrls, 'add', this.addOne);
            this.listenTo(app.CheckUrls, 'reset', this.addAll);
            this.listenTo(app.CheckUrls, 'remove', this.addAll);
            this.listenTo(app.CheckUrls, 'all', this.render);

            app.listener.on('checkurl:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
                var link = app.CheckUrls.get(parseInt(data.model.id));

                if (link) {
                    link.set(data.model);
                }
            });

            app.listener.on('checkurl:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
                if (parseInt(data.model.project_id) === parseInt(app.project_id)) {
                    app.CheckUrls.add(data.model);
                }
            });

            app.listener.on('checkurl:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
                var link = app.CheckUrls.get(parseInt(data.model.id));

                if (link) {
                    app.CheckUrls.remove(link);
                }
            });
        },
        render: function () {
            if (app.CheckUrls.length) {
                $('#no_checkurls').hide();
                $('#checkurl_list').show();
            } else {
                $('#no_checkurls').show();
                $('#checkurl_list').hide();
            }
        },
        addOne: function (url) {
            var view = new app.CheckUrlView({
                model: url
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.CheckUrls.each(this.addOne, this);
        }
    });

    app.CheckUrlView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editUrl'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#checkUrls-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            if (parseInt(data.last_status) === FAILED) {
                data.status_css = 'danger';
                data.icon_css   = 'warning';
                data.status     = Lang.get('checkUrls.failed');
            } else {
                data.status_css = 'success';
                data.icon_css   = 'check';
                data.status     = Lang.get('checkUrls.successful');
            }

            data.interval_label = data.period + ' ' + Lang.get('checkUrls.length');

            // data.report = Lang.get('app.no');

            // if (data.is_report) {
            //     data.report = Lang.get('app.ues');
            // }

            this.$el.html(this.template(data));

            return this;
        },
        editUrl: function() {
            $('#url_id').val(this.model.id);
            $('#title').val(this.model.get('title'));
            $('#url').val(this.model.get('url'));
            $('#period_' + this.model.get('period')).prop('checked', true);
            $('#is_report').prop('checked', this.model.get('is_report'));
        }
    });

})(jQuery);
