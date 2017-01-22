var app = app || {};

(function ($) {

    var editor;
    var previewfile;

    $('#configfile, #view-configfile').on('hidden.bs.modal', function (event) {
        editor.destroy();
    });

    $('#view-configfile').on('show.bs.modal', function (event) {
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
    $('#configfile').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.get('configFiles.create');

        editor = ace.edit('config-file-content');

        var filename = $('#config-file-path').val();
        var extension = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();

        if (extension === 'php' || extension === 'ini') {
            editor.getSession().setMode('ace/mode/' + extension);
        } else if (extension === 'yml') {
            editor.getSession().setMode('ace/mode/yaml');
        }

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');
        $('.label-danger', modal).remove();

        if (button.hasClass('btn-edit')) {
            title = Lang.get('configFiles.edit');
            $('.btn-danger', modal).show();
        } else {
            $('#config_file_id').val('');
            $('#config-file-name').val('');
            $('#config-file-path').val('');
            editor.setValue('');
            editor.gotoLine(1);
        }

        modal.find('.modal-title span').text(title);
    });


    // FIXME: This seems very wrong
    $('#configfile button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var file = app.ConfigFiles.get($('#config_file_id').val());

        file.destroy({
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

    // FIXME: This seems very wrong
    $('#configfile button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var config_file_id = $('#config_file_id').val();

        if (config_file_id) {
            var file = app.ConfigFiles.get(config_file_id);
        } else {
            var file = new app.ConfigFile();
        }

        file.save({
            name:        $('#config-file-name').val(),
            path:        $('#config-file-path').val(),
            content:     editor.getValue(),
            target_type: $('input[name="target_type"]').val(),
            target_id:   parseInt($('input[name="target_id"]').val())
        }, {
            wait: true,
            success: function(model, response, options) {
                dialog.modal('hide');
                $('.callout-danger', dialog).hide();

                icon.removeClass('fa-refresh fa-spin').addClass('fa-save');
                $('button.close', dialog).show();
                dialog.find('input').removeAttr('disabled');

                if (!config_file_id) {
                    app.ConfigFiles.add(response);
                }

                editor.setValue('');
                editor.gotoLine(1);
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
                        var parent = element.parents('div.form-group');
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

    app.ConfigFile = Backbone.Model.extend({
        urlRoot: '/config-file'
    });

    var ConfigFiles = Backbone.Collection.extend({
        model: app.ConfigFile
    });

    app.ConfigFiles = new ConfigFiles();

    app.ConfigFilesTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#configfile_list tbody');

            $('#no_configfiles').show();
            $('#configfile_list').hide();

            this.listenTo(app.ConfigFiles, 'add', this.addOne);
            this.listenTo(app.ConfigFiles, 'reset', this.addAll);
            this.listenTo(app.ConfigFiles, 'remove', this.addAll);
            this.listenTo(app.ConfigFiles, 'all', this.render);

            app.listener.on('configfile:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
                var file = app.ConfigFiles.get(parseInt(data.model.id));

                if (file) {
                    file.set(data.model);
                }
            });

            app.listener.on('configfile:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
                var target_type = $('input[name="target_type"]').val();
                var target_id = $('input[name="target_id"]').val();
                if (target_type == data.model.target_type && parseInt(data.model.target_id) === parseInt(target_id)) {
                    app.ConfigFiles.add(data.model);
                }
            });

            app.listener.on('configfile:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
                var file = app.ConfigFiles.get(parseInt(data.model.id));

                if (file) {
                    app.ConfigFiles.remove(file);
                }
            });
        },
        render: function () {
            if (app.ConfigFiles.length) {
                $('#no_configfiles').hide();
                $('#configfile_list').show();
            } else {
                $('#no_configfiles').show();
                $('#configfile_list').hide();
            }
        },
        addOne: function (file) {

            var view = new app.ConfigFileView({
                model: file
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.ConfigFiles.each(this.addOne, this);
        }
    });

    app.ConfigFileView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editFile',
            'click .btn-view': 'viewFile'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#config-files-template').html());
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
            $('#config_file_id').val(this.model.id);
            $('#config-file-name').val(this.model.get('name'));
            $('#config-file-path').val(this.model.get('path'));
            $('#config-file-content').text(this.model.get('content'));
        }
    });

})(jQuery);
