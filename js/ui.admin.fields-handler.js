(function ($) {

    panels.addInputFieldEventHandlers = function ( $this ) {

        $('html').trigger( 'pootlepb_admin_input_field_event_handlers', [ $this ] );

        /* Removing existing event handlers */
        $this.find('.upload-button').off('click');
        $this.find('.video-upload-button').off('click');

        // Uploading Fields aka media selection
        var ppbFileFrame,
            ppbMP4VideoFrame,
            ppbWebmVidFrame;
        $this.find('.upload-button').on('click', function (event) {
            event.preventDefault();

            $textField = $(this).siblings('input');

            // If the media frame already exists, reopen it.
            if (ppbFileFrame) {
                ppbFileFrame.open();
                return;
            }

            // Create the media frame.
            ppbFileFrame = wp.media.frames.ppbFileFrame = wp.media({
                title: 'Choose Background Image',
                button: {text: 'Set As Background Image'},
                multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            ppbFileFrame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = ppbFileFrame.state().get('selection').first().toJSON();

                // Do something with attachment.id and/or attachment.url here
                $textField
                    .val(attachment.url)
                $textField.change();

            });

            // Finally, open the modal
            ppbFileFrame.open();
        });

        $this.find('.video-upload-button').on('click', function (event) {
            event.preventDefault();

            $textField = $(this).siblings('input');

            // If the media frame already exists, reopen it.
            if (ppbMP4VideoFrame) {
                ppbMP4VideoFrame.open();
                return;
            }

            // Create the media frame.
            ppbMP4VideoFrame = wp.media.frames.ppbMP4VideoFrame = wp.media({
                title: 'Choose MP4/WEBM Background Video File',
                library: {
                    type: 'video'
                },
                button: {
                    text: 'Set As Background Video'
                },
                multiple: false
            });

            // When an image is selected, run a callback.
            ppbMP4VideoFrame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = ppbMP4VideoFrame.state().get('selection').first().toJSON();

                // Do something with attachment.id and/or attachment.url here
                $textField.val(attachment.url);
                $textField.change();

            });

            // Finally, open the modal
            ppbMP4VideoFrame.open();
        });

        $this.find('.ppb-slider').each(function() {
            var $t = $(this),
                $f = $t.siblings('input'),
                $spn = $(this).siblings('.slider-val'),
                max = $t.data('max'),
                valu;

            //Update span
            if ( '' != $f.val() ) {
                valu = $f.val();
            } else {
                valu = $t.data('default');
            }
            $spn.text((valu/max*100) + '%');

            //Init slider
            $t.slider({
                min: $t.data('min'),
                max: max,
                step: $t.data('step'),
                value: valu,
                slide: function (e, ui) {
                    //Update values on slide
                    $f.val(ui.value);
                    $spn.text(Math.round(ui.value/max*100) + '%');
                },
                change: function (e, ui) {
                    //Update values on slide
                    $f.val(ui.value);
                    $spn.text(Math.round(ui.value/max*100) + '%');
                }
            });
        });
    };

    panels.bgVideoMobImgSet = function(){

        $('#row-bg-video-notice').remove();
        $('#pp-pb-bg_mobile_image').attr('style', '');

        if( '.bg_video' == $('#pp-pb-background_toggle').val() && $('#pp-pb-bg_video').val() ) {

            if( '' == $('#pp-pb-bg_mobile_image').val().replace( ' ', '' ) ) {

                var notice = $('<div/>').attr('id', 'row-bg-video-notice').addClass('ppb-alert')
                    .html('<span class="dashicons dashicons-no"></span>Please select an image to display instead of the background video for mobile devices.')
                $('.bg_section.bg_video').append(notice);
                $('#pp-pb-bg_mobile_image').css({backgroundColor: '#fcc', borderColor: '#f88'});

                return false;
            }
        }

        return true;
    }

})(jQuery);