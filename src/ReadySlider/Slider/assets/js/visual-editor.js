/*global jQuery*/

(function ($, _wp, _app, _debug, undefined) {

    var _log = (function (message, channel) {
        channel = channel || 'veditor';

        if (_debug) {
            console.log(channel + ': ' + message);
        }
    });

    function Colorpicker(selector, context) {
        if (selector == undefined) {
            _log('Selector is undefined', 'colorpicker');

            return;
        }

        if (context == undefined) {
            this.$elements = $(selector);
        }

        this.$elements = $(selector, context);
    }

    Colorpicker.prototype = {
        init: function () {
            $(this.$elements.selector).wpColorPicker();
        }
    };

    function Designer(window) {
        this.window = window;
        this.container = null;
    }

    Designer.prototype = {
        loadTo: function (el) {
            if (el == undefined) {
                _log('Settings container is undefined', 'designer');

                return;
            }

            if (!el instanceof jQuery) {
                el = $(el);
            }

            this.container = el;
            this.activeClass = 'rs-veditor-active';
        },
        load:   function (selector, callback) {
            var _self = this, sanitize = function (str) {
                return $.trim(str.replace(_self.activeClass, ''));
            };

            this.save(function () {
                var $inputs = $('.rs-veditor-input'), data = {};

                $inputs.each(function () {
                    if (!$.isArray(data[$(this).data('selector')])) {
                        data[$(this).data('selector')] = [];
                    }

                    data[$(this).data('selector')].push($(this).val());
                });

                return data;
            });

            this.window.loading(this.container);

            $.post(_wp.ajax.settings.url, {
                action: 'ready-slider',
                route:  {
                    module: 'slider',
                    action: 'designer'
                },
                slider_id: _self.window.getWindow().data('id'),
                selector:  sanitize(selector)
            }).success(function (response) {
                var form = $(response.template);

                _self.container.empty().append(form);

                form.find('.rs-veditor-input')
                    .bind('change paste keyup input blur focusout', function (e) {
                        var $input = $(this);

                        $($input.data('selector')).css($input.data('prop'), $input.val());
                    });


                if ($.isFunction(callback)) {
                    callback(response);
                }
            });
        },
        handle: function (selectors, context) {
            var _self = this,
                colorpicker = new Colorpicker('.rs-veditor-colorpicker', context),
                $triggers = $(selectors.join(','), context);

            $triggers.on('click', function (e) {
                e.stopImmediatePropagation();

                $triggers.removeClass(_self.activeClass);
                $( this ).addClass(_self.activeClass);

                _self.load(e.currentTarget.className, $.proxy(colorpicker.init, colorpicker));
            })
            .first()
            .trigger('click');
        },
        save: function (settigs) {
            var _self = this;

            if ($.isFunction(settings)) {
                settings = settings();
            }

            $.post(_wp.ajax.settings.url, {
                action: 'ready-slider',
                route: {
                    module: 'slider',
                    action: 'saveDesign'
                },
                slider_id: _self.window.getWindow().data('id'),
                design: settings
            }).success(function (response) {
                console.log(response);
            });
        }
    };

    function WindowController(selector) {
        // Private properties
        var
            // jQuery window object.
            wnd,

            // Loaded slider
            slider,

            // Loaded container
            container,

            // Loaded sidebar
            sidebar,

            // Total window width
            totalWidth = 900,

            // Total sidebar width
            sidebarWidth = 300,

            // Window defaults
            defaults = {
                width: totalWidth,
                autoOpen: _debug,
                modal: true
            };

        // Public properties
        this.config = $.extend({}, defaults);
        this.sliderWidth = Math.floor((totalWidth - sidebarWidth) - 14.3 * 2);
        this.containerWidth = totalWidth - sidebarWidth - 1;
        this.sidebarWidth = totalWidth - this.containerWidth - 14.3 * 2;

        if (selector == undefined) {
            _log('Selector is undefined', 'window-controller');

            return;
        }

        wnd = $(selector);

        // Privileged members
        this.getWindow = function () {
            return $(wnd.selector);
        }
    }

    WindowController.prototype = {

        // Request plugin for slider.
        request: function (onRequestSuccess) {
            var _self = this;

            $.post(_wp.ajax.settings.url, {

                // AJAX request settings.
                action: 'ready-slider',
                route: {
                    module: 'slider',
                    action: 'getPreview'
                },

                // Slider identifier.
                id:    _self.getWindow().data('id'),

                // Slider width.
                width: _self.sliderWidth,
            }).success($.proxy(_self.load, _self));
        },

        // Loads window content
        load: function (response) {
            var _self = this, wnd = this.getWindow().empty(), container, sidebar, slider;

            slider = $(response.slider);

            // Create slider container in window.
            container = $('<div/>', { id: 'veditor-container' }).css({
                width: _self.containerWidth,
                float: 'left',
            }).appendTo(wnd);

            // Create sidebar
            sidebar = $('<div/>', { id: 'veditor-sidebar' }).css({
                width: _self.sidebarWidth,
                float: 'left'
            }).appendTo(wnd);

            // Append slider to the container.
            container.append(slider);

            // Show loading at sidebar before we load some content here.
            this.loading(sidebar);

            // Init slider.
            _app.init();

            // Designer
            var designer = new Designer( this );

            designer.loadTo(sidebar);
            designer.handle(['.bx-viewport', '.bx-caption', '.bx-pager'], slider);
        },

        loading: function (el) {
            if (el == undefined) {
                el = this.getWindow();
            }

            if (!el instanceof jQuery) {
                el = $(el);
            }

            var path = 'images/wpspin_light.gif',
                src  = _wp.ajax.settings.url.replace('admin-ajax.php', path),
                css  = {
                    display: 'block',
                    margin: '25px auto 10px auto',
                    float: 'none'
                };

            $('<img/>', { src: src }).css(css).appendTo(el.empty());

            return el;
        },

        init: function (config) {
            var _self = this;

            if ($.type(config) == 'object') {
                this.config = $.extend({}. this.config, config);
            }

            this.config = $.extend({}, this.config, {
                buttons: {
                    Save: function () {

                    },
                    Close: function () {
                        _self.getWindow().dialog('close');
                    }
                },
                open: function () {
                    if (_self.getWindow().data('loaded') == true) {
                        return;
                    }

                    _self.request();
                }
            });

            // Window's data isn't loaded yet when we create new dialog.
            // It's need to create markup and load slider only one time.
            this.getWindow().data('loaded', false);

            // Initialize new dialog.
            this.getWindow().dialog(this.config);
        }
    };

    function Controller() {

    }

    $(document).ready(function () {
        var dialog = new WindowController('#visual-editor-window');

        dialog.init();

        console.log(dialog);
    });

}(jQuery, wp, ReadySlider, document.location.hash == '#debug'));
