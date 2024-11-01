(function ($) {
    $.fn.refresh = function () {
        return $(this.selector);
    };
}(jQuery));

(function (app, $) {

    function Loader() {
        this.$overlay = $('.gg-modal-loading-overlay');
        this.$content = $('.gg-modal-loading-object');
        this.$loadingText = this.$content.find('span#loading-text');

        this.defaultText = this.$loadingText.text();
    }

    Loader.prototype.clearText = function () {
        this.$loadingText.text(this.defaultText);
    };

    Loader.prototype.show = function (text) {
        this.$overlay.slideDown($.proxy(function () {
            if (typeof text !== 'undefined') {
                this.$loadingText.text(text);
            }

            this.$content.show();
        }, this));
    };

    Loader.prototype.hide = function () {
        // Chrome bug ?
        setTimeout($.proxy(function () {
            this.$content.hide($.proxy(function () {
                this.$overlay.slideUp();
                this.clearText();
            }, this));
        }, this), 1500)
    };

    $(document).ready(function () {
        app.Loader = new Loader;
    });

}(window.ReadyGridGallery = window.ReadyGridGallery || {}, jQuery));

(function (app, $) {

    function Forms() {}

    Forms.prototype.preventSubmit = function (submitEvent) {
        submitEvent.preventDefault();
        return false;
    };

    $(document).ready(function () {
        app.Forms = new Forms;

        $('[data-prevent-submit]').submit(app.Forms.preventSubmit);
    });

}(window.ReadyGridGallery = window.ReadyGridGallery || {}, jQuery));

(function($) {

    $(document).ready(function() {

        // ReadyGridGallery.Loader.show();

        /* Tooltipster */
        $('.ready-tooltip').tooltipster({
            contentAsHTML: true
        });

        /* Lazy loading */
        $('.ready-lazy').lazyload({
            effect: 'fadeIn',
            load: function() {
                setContainerHeight();
            }
        });

        setContainerHeight();
        changeUiButtonToWp();
        closeOnOutside();
    });

    $(window).on('resize', function() {
        setContainerHeight();
    });

    function setContainerHeight() {
        var container = $('.ready-container'),
            content = $('.ready-content'),
            navigation = $('.ready-navigation ul');

        navigation.css({'height': 'auto' });
        container.css({'height': 'auto' });
        content.css({'height': 'auto' });

        if (content.outerHeight() > navigation.outerHeight() || container.outerHeight > navigation.outerHeight()) {
            navigation.css({'height': content.outerHeight() + 'px'});
        } else {
            container.css({'height': navigation.outerHeight() + 'px'});
            content.css({'height': navigation.outerHeight() + 'px'});
        }
    }

    function changeUiButtonToWp() {
        $(document).on('dialogopen', function(event, ui) {
            var $button = $('.ui-button');

            $button.each(function() {
                if (!$(this).hasClass('ui-dialog-titlebar-close')) {
                    $(this).removeAttr('class').addClass('button button-primary');
                }
            });
        });
    }

    function closeOnOutside() {
        $(document).on('click', function () {
            var $overlay = $('.ui-widget-overlay');
            var $container = $('body').find('.ui-dialog-content');

            $overlay.on('click', function () {
                $container.dialog('close');
            });
        });
    }

})(jQuery);
