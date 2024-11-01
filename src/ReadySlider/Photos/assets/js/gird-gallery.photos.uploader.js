(function ($) {

    var defaults = {
        wp:         null,
        url:        null,
        title:      'Choose image',
        buttonText: 'Choose image',
        debug:      false,
        multiple:   true,

        route: {
            module: 'photos',
            action: 'add'
        }
    };

    var uploader;

    $.fn.ggPhotoUploader = function (parameters) {
        parameters = $.extend({}, defaults, parameters);


        if (typeof(parameters.wp) === 'undefined') {
            $.jGrowl('The WordPress Media API is not available.');
            return;
        }

        parameters.url = parameters.wp.ajax.settings.url;

        uploader = parameters.wp.media.frames.file_frame = parameters.wp.media({
            title:    parameters.title,
            button:   {
                text: parameters.buttonText
            },
            multiple: parameters.multiple
        });

        uploader.on('select', function () {
            ReadyGridGallery.Loader.show('Please, wait until images will be imported.');

            var attachments = uploader.state().get('selection').toJSON(),
                statusMessage = null;

            if (attachments.length > 1) {
                statusMessage = 'There are %number% photos selected';
            } else {
                statusMessage = 'There is %number% photo selected';
            }

            $.jGrowl(statusMessage.replace('%number%', attachments.length.toString()));

            var $container = $('[data-container]');

            $.each(attachments, function (index, attachment) {
                $.post(parameters.url, {
                    action:        'ready-slider',
                    route:         parameters.route,
                    attachment_id: attachment.id,
                    folder_id:     $('[data-upload]').data('folder-id'),
                    view_type:     $container.data('container')
                }, function (response) {
                    if (!response.error) {
                        $container.parents('#containerWrapper').show(function () {
                            $('#gg-alrt').remove();
                        });

                        $container.prepend(response.photo);
                        $('.ready-lazy').lazyload();
                    }

                    $.jGrowl(response.message);
                });
            });

            ReadyGridGallery.Loader.hide();
        });

        this.on('click', function (e) {
            e.preventDefault();
            uploader.open();
        });
    };

})(jQuery);
