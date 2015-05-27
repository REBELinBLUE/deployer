var app = app || {};

(function ($) {

    var editor;
    var previewfile;

    $('#projectfile, #view-projectfile').on('hidden.bs.modal', function (event) {
        editor.destroy();
    });

    $('#view-projectfile').on('show.bs.modal', function (event) {
        editor = ace.edit('preview-content');
        editor.setReadOnly(true);
        editor.getSession().setUseWrapMode(true);

        var extension = previewfile.substr(previewfile.lastIndexOf('.') + 1).toLowerCase();

        if (extension === 'php' || extension === 'ini') {
            editor.getSession().setMode('ace/mode/' + extension);
        } else if (extension === 'yml') {
            editor.getSession().setMode('ace/mode/yaml');
        }
    });

    // FIXME: This seems very wrong
    $('#projectfile').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.projectFiles.create;

        editor = ace.edit('project-file-content');

        var filename = $('#project-file-path').val();
        var extension = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();

        if (extension === 'php' || extension === 'ini') {
            editor.getSession().setMode('ace/mode/' + extension);
        } else if (extension === 'yml') {
            editor.getSession().setMode('ace/mode/yaml');
        }

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');

        if (button.hasClass('btn-edit')) {
            title = Lang.projectFiles.edit;
            $('.btn-danger', modal).show();
        } else {
            $('#project_file_id').val('');
            $('#project-file-name').val('');
            $('#project-file-path').val('');
            editor.setValue('');
            editor.gotoLine(1);
        }

        modal.find('.modal-title span').text(title);
    });


    // FIXME: This seems very wrong
    $('#projectfile button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var file = app.ProjectFiles.get($('#project_file_id').val());

        file.destroy({
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                app.ProjectFiles.remove(file);
            },
            error: function() {
                icon.removeClass('fa-refresh fa-spin').addClass('fa-trash');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');
            }
        });
    });

    // FIXME: This seems very wrong
    $('#projectfile button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var project_file_id = $('#project_file_id').val();

        if (project_file_id) {
            var file = app.ProjectFiles.get(project_file_id);
        } else {
            var file = new app.ProjectFile();
        }

        file.save({
            name:       $('#project-file-name').val(),
            path:       $('#project-file-path').val(),
            content:    editor.getValue(),
            project_id: $('input[name="project_id"]').val()
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!project_file_id) {
                    app.ProjectFiles.add(response);
                }

                editor.setValue('');
                editor.gotoLine(1);
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

    app.ProjectFile = Backbone.Model.extend({
        urlRoot: '/project-file',
        poller: false
    });

    var ProjectFiles = Backbone.Collection.extend({
        model: app.ProjectFile
    });

    app.ProjectFiles = new ProjectFiles();

    app.ProjectFilesTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#projectfile_list tbody');

            $('#no_projectfiles').show();
            $('#projectfile_list').hide();

            this.listenTo(app.ProjectFiles, 'add', this.addOne);
            this.listenTo(app.ProjectFiles, 'reset', this.addAll);
            this.listenTo(app.ProjectFiles, 'all', this.render);
        },
        render: function () {
            if (app.ProjectFiles.length) {
                $('#no_projectfiles').hide();
                $('#projectfile_list').show();
            } else {
                $('#no_projectfiles').show();
                $('#projectfile_list').hide();
            }
        },
        addOne: function (file) {

            var view = new app.ProjectFileView({ 
                model: file
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.ProjectFiles.each(this.addOne, this);
        }
    });

    app.ProjectFileView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editFile',
            'click .btn-view': 'viewFile'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#project-files-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            this.$el.html(this.template(data));

            return this;
        },
        viewFile: function() {
            previewfile = this.model.get('path');
            $('#preview-content').text(this.model.get('content'));
        },
        editFile: function() {
            // FIXME: Sure this is wrong?
            $('#project_file_id').val(this.model.id);
            $('#project-file-name').val(this.model.get('name'));
            $('#project-file-path').val(this.model.get('path'));
            $('#project-file-content').text(this.model.get('content'));
        }
    });

})(jQuery);