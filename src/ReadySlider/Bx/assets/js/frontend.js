/*global jQuery*/
(function ($, app) {

    /**
     * Converts string to boolean values if it needed.
     * @type {Function}
     * @param {*} value
     * @return {*}
     */
    var stringToBoolean = (function (value) {
        if (value == 'true') {
            return true;
        } else if (value == 'false') {
            return false;
        } else {
            return value;
        }
    });

    var defaults = {
        adaptiveHeight: false,
        responsive:     true
    };

    var init = (function ($container) {
        var $bx;

        if (!$container.length) {
            return;
        }

        $bx = $container.filter('.ready-slider-bx');

        if (!$bx.length) {
            return;
        }

        $.each($bx, function (index, slider) {
            var $slider = $(slider),
                settings = $slider.data('settings'),
                config = {};

            $.each(settings, function (category, opts) {
                $.each(opts, function (key, value) {
                    if (key !== 'enabled') {
                        config[key] = stringToBoolean(value);
                    }
                });
            });

            config = $.extend(defaults, config, { slideWidth: config.width });

            $slider.find('ul').css({ visibility: 'hidden'}).each(function (index, container) {
                $(container).bxSlider(
                    $.extend(config,
                        {
                            onSliderLoad: function () {
                                $(container).css({ visibility: 'visible' });
                            }
                        }
                    )
                );
            });
        });
    });

    app.plugins = app.plugins || {};
    app.plugins.bx = init;

}(jQuery, window.ReadySlider = window.ReadySlider || {}));
