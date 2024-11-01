/*global jQuery*/
(function ($, WordPress) {

    /**
     * Page controller.
     * @constructor
     */
    function Controller() {
        this.$newSlider = $('#newSliderDialog');
        this.$form = $('#newSliderDialogForm');

        this.init();
    }

    /**
     * Opens "New Slider" dialog.
     *
     * @type {Function}
     */
    Controller.prototype.open = (function () {
        this.$newSlider.dialog('open');
    });

    /**
     * Submits a form.
     *
     * @type {Function}
     */
    Controller.prototype.submitForm = (function () {
        this.$form.submit();
    });

    /**
     * Deletes the slider.
     * Sends request to the ReadySlider_Slider_Controller::deleteAction().
     * If slider deleted successfully, then add animation and remove element from the page.
     * Otherwise show error message with $.jGrowl.
     *
     * @type {Function}
     */
    Controller.prototype.delete = (function (e) {
        e.preventDefault();

        if (!confirm('Are you sure?')) {
            return;
        }

        /**
         * Finds the parent of the event's target
         * retrieves element id and returns slider id.
         *
         * @returns {Number}
         */
        var getSliderId = function (target) {
            var elId = $(target)
                .parents('.gg-slider')
                .attr('id');

            return parseInt(elId.replace('slider-', ''), 10);
        };

        $.post(WordPress.ajax.settings.url, {
            action: 'ready-slider',
            id:     getSliderId(e.currentTarget),
            route:  {
                module: 'slider',
                action: 'delete'
            }
        }).success(function (response) {
            if (response.error) {
                $.jGrowl(response.message);

                return;
            }


            $(e.currentTarget)
                .parents('.gg-slider')
                .addClass('hinge animated')
                .fadeOut(2000, function () {
                    $(this).remove();
                });
        });
    });

    /**
     * Controller initialization.
     *
     * Here we are init "New Slider" dialog and handles form submission.
     *
     * @type {Function}
     */
    Controller.prototype.init = (function () {
        this.$newSlider.dialog({
            width:    this.$newSlider.data('width'),
            autoOpen: this.$newSlider.data('auto-open'),
            buttons: {
                OK: $.proxy(function () {
                    this.submitForm();
                    this.$newSlider.dialog('close');
                }, this),
                Cancel: $.proxy(function () {
                    this.$newSlider.dialog('close');
                }, this)
            }
        });

        this.$form.submit(function (e) {
            e.preventDefault();

            /**
             * Converts $.serializeArray() result to key-value pairs object.
             *
             * @param {Array} input $.serializeArray() result
             * @return {Object} Key-value pairs.
             */
            var toObject = (function (input) {
                var result = {};

                $.each(input, function (index, object) {
                    result[object.name] = object.value;
                });

                return result;
            });

            var data = toObject($(e.currentTarget).serializeArray());

            $.post(WordPress.ajax.settings.url, data)
                .success(function (response) {
                    if (response.success) {
                        window.location.href = response.url;
                    }

                    $.jGrowl(response.message);
                });

        });

        if (document.location.hash == '#addSliderWindow') {
            this.$newSlider.dialog('open');
        }
    });

    $(document).ready(function () {
        var controller = new Controller();

        $('#add-slider').on('click', $.proxy(controller.open, controller));
        $('.delete-gallery').on('click', $.proxy(controller.delete, controller));
    });

}(jQuery, window.wp = window.wp || {}));