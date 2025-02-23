/*global jQuery*/

(function (app, url, $) {
    function Post(route, data) {

        this.url = url;
        this.route = route;
        this.data = {
            action: 'ready-slider'
        };

        if (typeof data !== 'undefined') {
            this.data = $.extend(this.data, data);
        }

        return this;
    }

    Post.prototype.add = function (key, value) {
        if (key === 'action') {
            throw new Error('Invalid key: "action"');
        }

        this.data[key] = value;

        return this;
    };

    Post.prototype.send = function (fn) {

        this.data.route = this.route;

        $.post(this.url, this.data, $.proxy(function (response, status) {
            fn(response, this.data);
        }, this));

    };

    app.Ajax = app.Ajax || {};
    app.Ajax.Post = function (route, data) {
        return new Post(route, data);
    };

}(window.ReadyGridGallery = window.ReadyGridGallery || {}, ajaxurl, jQuery));
