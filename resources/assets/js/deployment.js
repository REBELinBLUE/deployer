var app = app || {};

(function ($) {
    app.ServerLog = Backbone.Model.extend({
    });

    var Deployment = Backbone.Collection.extend({
        model: app.ServerLog
    });

    app.Deployment = new Deployment();
})(jQuery);