var app = app || {};

(function ($) {
    $('#checkurl').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.CheckUrls.create;

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        if (button.hasClass('btn-edit')) {
            title = Lang.CheckUrls.edit;
            $('.btn-danger', modal).show();
        } else {
            $('#url_id').val('');
            $('#title').val('');
            $('#url').val('');
            $('#period').val(5);
            $('#is_report').prop('checked',false);
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

                app.CheckUrls.remove(file);
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
            title:       $('#title').val(),
            url:    $('#url').val(),
            is_report: $('#is_report').prop('checked'),
            period: $('#period').val(),
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

    app.CheckUrl = Backbone.Model.extend({
        urlRoot: '/check-url',
        poller: false
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
            this.listenTo(app.CheckUrls, 'all', this.render);
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
            if (data.last_status) {
                data.status_css = 'danger';
            } else {
                data.status_css = 'success';
            }

            this.$el.html(this.template(data));

            return this;
        },
        editUrl: function() {
            $('#url_id').val(this.model.id);
            $('#title').val(this.model.get('title'));
            $('#url').val(this.model.get('url'));
            $('#period').val(this.model.get('period'));
            $('#is_report').prop('checked', this.model.get('is_report'));
        }
    });

})(jQuery);