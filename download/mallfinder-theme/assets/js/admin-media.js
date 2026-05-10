/**
 * MallFinder Admin Media Upload
 *
 * @package MallFinder
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        var mediaUploader;

        $('.upload-logo-btn').on('click', function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Select Store Logo',
                button: {
                    text: 'Use this image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                $('#store_logo').val(attachment.id);
                
                $('.logo-preview').html(
                    '<img src="' + attachment.url + '" style="max-width: 150px; height: auto;">'
                );
                
                $('.remove-logo-btn').show();
            });

            mediaUploader.open();
        });

        $('.remove-logo-btn').on('click', function(e) {
            e.preventDefault();
            
            $('#store_logo').val('');
            $('.logo-preview').empty();
            $(this).hide();
        });
    });

})(jQuery);
