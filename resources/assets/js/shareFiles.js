var app = app || {};

(function ($) {
    // FIXME: This seems very wrong
    $('#sharefile').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = 'Add file or folder';

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        if (button.hasClass('btn-edit')) {
            title = 'Edit file';
            $('.btn-danger', modal).show();
        } else {
            $('#file_id').val('');
            $('#name').val('');
            $('#file').val('');
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#sharefile button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var file = app.SharedFiles.get($('#file_id').val());

        file.destroy({
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                app.SharedFiles.remove(file);
            },
            error: function() {
                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        })
    });

    // FIXME: This seems very wrong
    $('#sharefile button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var file_id = $('#file_id').val();

        if (file_id) {
            var file = app.SharedFiles.get(file_id);
        } else {
            var file = new app.SharedFile();
        }

        file.save({
            name:       $('#name').val(),
            file:    $('#file').val(),
            project_id: $('input[name="project_id"]').val()
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!file_id) {
                    app.SharedFiles.add(response);
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

    app.SharedFile = Backbone.Model.extend({
        urlRoot: '/shared-files',
        poller: false
    });

    var SharedFiles = Backbone.Collection.extend({
        model: app.SharedFile
    });

    app.SharedFiles = new SharedFiles();

    app.SharedFilesTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#file_list tbody');

            $('#no_files').show();
            $('#file_list').hide();

            this.listenTo(app.SharedFiles, 'add', this.addOne);
            this.listenTo(app.SharedFiles, 'reset', this.addAll);
            this.listenTo(app.SharedFiles, 'all', this.render);
        },
        render: function () {
            if (app.SharedFiles.length) {
                $('#no_files').hide();
                $('#file_list').show();
            } else {
                $('#no_files').show();
                $('#file_list').hide();
            }
        },
        addOne: function (file) {

            var view = new app.FileView({ 
                model: file
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.SharedFiles.each(this.addOne, this);
        }
    });

    app.FileView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editFile'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#files-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            this.$el.html(this.template(data));

            return this;
        },
        editFile: function() {
            // FIXME: Sure this is wrong?
            $('#file_id').val(this.model.id);
            $('#name').val(this.model.get('name'));
            $('#file').val(this.model.get('file'));
        }
    });

})(jQuery);