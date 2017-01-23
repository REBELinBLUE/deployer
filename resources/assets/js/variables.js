var app = app || {};

(function ($) {
   // FIXME: This seems very wrong
    $('#variable').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var title = Lang.get('variables.create');

        $('.btn-danger', modal).hide();
        $('.callout-danger', modal).hide();
        $('.has-error', modal).removeClass('has-error');
        $('.label-danger', modal).remove();

        if (button.hasClass('btn-edit')) {
            title = Lang.get('variables.edit');
            $('.btn-danger', modal).show();
        } else {
            $('#variable_id').val('');
            $('#variable_name').val('');
            $('#variable_value').val('');
        }

        modal.find('.modal-title span').text(title);
    });

    // FIXME: This seems very wrong
    $('#variable button.btn-delete').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-trash');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var variable = app.Variables.get($('#variable_id').val());

        variable.destroy({
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
    $('#variable button.btn-save').on('click', function (event) {
        var target = $(event.currentTarget);
        var icon = target.find('i');
        var dialog = target.parents('.modal');

        icon.addClass('fa-refresh fa-spin').removeClass('fa-save');
        dialog.find('input').attr('disabled', 'disabled');
        $('button.close', dialog).hide();

        var variable_id = $('#variable_id').val();

        if (variable_id) {
            var variable = app.Variables.get(variable_id);
        } else {
            var variable = new app.Variable();
        }

        variable.save({
            name:        $('#variable_name').val(),
            value:       $('#variable_value').val(),
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

                if (!variable_id) {
                    app.Variables.add(response);
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

    app.Variable = Backbone.Model.extend({
        urlRoot: '/variables',
        initialize: function() {

        }
    });

    var Variables = Backbone.Collection.extend({
        model: app.Variable
    });

    app.Variables = new Variables();

    app.VariablesTab = Backbone.View.extend({
        el: '#app',
        events: {

        },
        initialize: function() {
            this.$list = $('#variable_list tbody');

            $('#variable_list').hide();

            this.listenTo(app.Variables, 'add', this.addOne);
            this.listenTo(app.Variables, 'reset', this.addAll);
            this.listenTo(app.Variables, 'remove', this.addAll);
            this.listenTo(app.Variables, 'all', this.render);

            app.listener.on('variable:REBELinBLUE\\Deployer\\Events\\ModelChanged', function (data) {
                $('#variable_' + data.model.id).html(data.model.name);

                var variable = app.Variables.get(parseInt(data.model.id));

                if (variable) {
                    variable.set(data.model);
                }
            });

            app.listener.on('variable:REBELinBLUE\\Deployer\\Events\\ModelCreated', function (data) {
                var target_type = $('input[name="target_type"]').val();
                var target_id = $('input[name="target_id"]').val();
                if (target_type == data.model.target_type && parseInt(data.model.target_id) === parseInt(target_id)) {
                    app.Variables.add(data.model);
                }
            });

            app.listener.on('variable:REBELinBLUE\\Deployer\\Events\\ModelTrashed', function (data) {
                var variable = app.Variables.get(parseInt(data.model.id));

                if (variable) {
                    app.Variables.remove(variable);
                }
            });
        },
        render: function () {
            if (app.Variables.length) {
                $('#variable_list').show();
            } else {
                $('#variable_list').hide();
            }
        },
        addOne: function (variable) {

            var view = new app.VariableView({
                model: variable
            });

            this.$list.append(view.render().el);
        },
        addAll: function () {
            this.$list.html('');
            app.Variables.each(this.addOne, this);
        }
    });

    app.VariableView = Backbone.View.extend({
        tagName:  'tr',
        events: {
            'click .btn-edit': 'editVariable'
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.template = _.template($('#variable-template').html());
        },
        render: function () {
            var data = this.model.toJSON();

            this.$el.html(this.template(data));

            return this;
        },
        editVariable: function() {
            $('#variable_id').val(this.model.id);
            $('#variable_name').val(this.model.get('name'));
            $('#variable_value').val(this.model.get('value'));
        }
    });
})(jQuery);
