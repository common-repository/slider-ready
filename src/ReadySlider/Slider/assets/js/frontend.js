/*global jQuery*/
(function ($, app, debug, undefined) {

    app.init = (function (selector) {
        var $container, defaultSelector = '.ready-slider';

        $container = (selector == undefined) ? $(defaultSelector) : $(selector);

        if (!$container.length) {
            if (debug) {
                console.log('Selector "' + selector + '" does not exists.');
            }

            return false;
        }

        if ($.isEmptyObject(app.plugins)) {
            if (debug) {
                console.log('There are no registered plugins.');
            }

            return false;
        }

        $.each(app.plugins, function (plugin, callback) {
            if (debug) {
                console.log('Plugin initialization: ' + plugin);
            }

            if (!$.isFunction(callback)) {
                if (debug) {
                    console.log('The callback for the ' + plugin + ' is not a function.');
                }
            }

            callback($container);
        });

        return true;
    });

    $(document).ready(function () {
        app.init();
    });

}(jQuery, window.ReadySlider = window.ReadySlider || {}, document.location.hash == '#debug'));