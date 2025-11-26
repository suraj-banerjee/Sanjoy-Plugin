(function ($) {
    $(document).ready(function () {
        console.log('Admin script loaded.');
        // Event listener for media upload buttons
        $('.skd-upload-media').on('click', function (e) {
            e.preventDefault();

            // Get the target input field from the button's data attribute
            const targetField = $(this).data('target');

            // Open a new media uploader instance
            const mediaUploader = wp.media({
                title: 'Select or Upload Media',
                button: {
                    text: 'Use This Media',
                },
                multiple: false, // Allow only one file
            });

            // When an image is selected, populate the corresponding input field
            mediaUploader.on('select', function () {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                $(targetField).val(attachment.url); // Set the URL in the targeted field
            });

            // Open the media uploader
            mediaUploader.open();
        });


        //price plan add/edit form
        $('#skd-pl-price-plan-add-edit #add_gst_rate').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #gst_fields').show();
                $('#skd-pl-price-plan-add-edit #is_free').prop('checked', false); // Uncheck Free if GST is selected
            } else {
                $('#skd-pl-price-plan-add-edit #gst_fields').hide();
            }
        });

        $('#skd-pl-price-plan-add-edit #is_free').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #price').val('').prop('disabled', true);
                $('#skd-pl-price-plan-add-edit #add_gst_rate').prop('checked', false).prop('disabled', true);
                $('#skd-pl-price-plan-add-edit #gst_fields').hide();
            } else {
                $('#skd-pl-price-plan-add-edit #price').prop('disabled', false);
                $('#skd-pl-price-plan-add-edit #add_gst_rate').prop('disabled', false);
            }
        });

        $('#skd-pl-price-plan-add-edit #never_expire').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #listing_duration').val('').prop('disabled', true);
                $('#skd-pl-price-plan-add-edit #duration_unit').prop('disabled', true);
            } else {
                $('#skd-pl-price-plan-add-edit #listing_duration').prop('disabled', false);
                $('#skd-pl-price-plan-add-edit #duration_unit').prop('disabled', false);
            }
        });

        $('#skd-pl-price-plan-add-edit #mark_as_unlimited').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #no_of_listing').val('').prop('disabled', true);
            } else {
                $('#skd-pl-price-plan-add-edit #no_of_listing').prop('disabled', false);
            }
        });

        $('#skd-pl-price-plan-add-edit #mark_feature_unlimited').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #no_of_feature_listing').val('').prop('disabled', true);
            } else {
                $('#skd-pl-price-plan-add-edit #no_of_feature_listing').prop('disabled', false);
            }
        });

        $('#skd-pl-price-plan-add-edit #location_fld').change(function () {
            $('#skd-pl-price-plan-add-edit #location_fld_limit').val('').prop('disabled', false);
            $('#skd-pl-price-plan-add-edit #location_fld_limit_unlimited').prop('checked', false);
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #location_fld_div').show();
            } else {
                $('#skd-pl-price-plan-add-edit #location_fld_div').hide();
            }
        });
        $('#skd-pl-price-plan-add-edit #location_fld_limit_unlimited').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #location_fld_limit').val('').prop('disabled', true);
            } else {
                $('#skd-pl-price-plan-add-edit #location_fld_limit').prop('disabled', false);
            }
        });

        $('#skd-pl-price-plan-add-edit #tag_fld').change(function () {
            $('#skd-pl-price-plan-add-edit #tag_fld_limit').val('').prop('disabled', false);
            $('#skd-pl-price-plan-add-edit #tag_fld_limit_unlimited').prop('checked', false);
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #tag_fld_div').show();
            } else {
                $('#skd-pl-price-plan-add-edit #tag_fld_div').hide();
            }
        });
        $('#skd-pl-price-plan-add-edit #tag_fld_limit_unlimited').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #tag_fld_limit').val('').prop('disabled', true);
            } else {
                $('#skd-pl-price-plan-add-edit #tag_fld_limit').prop('disabled', false);
            }
        });

        $('#skd-pl-price-plan-add-edit #category_fld').change(function () {
            $('#skd-pl-price-plan-add-edit #category_fld_limit').val('').prop('disabled', false);
            $('#skd-pl-price-plan-add-edit #category_fld_limit_unlimited').prop('checked', false);
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #category_fld_div').show();
            } else {
                $('#skd-pl-price-plan-add-edit #category_fld_div').hide();
            }
        });
        $('#skd-pl-price-plan-add-edit #category_fld_limit_unlimited').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #category_fld_limit').val('').prop('disabled', true);
            } else {
                $('#skd-pl-price-plan-add-edit #category_fld_limit').prop('disabled', false);
            }
        });

        $('#skd-pl-price-plan-add-edit #description_fld').change(function () {
            $('#skd-pl-price-plan-add-edit #description_fld_limit').val('').prop('disabled', false);
            $('#skd-pl-price-plan-add-edit #description_fld_limit_unlimited').prop('checked', false);
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #description_fld_div').show();
            } else {
                $('#skd-pl-price-plan-add-edit #description_fld_div').hide();
            }
        });
        $('#skd-pl-price-plan-add-edit #description_fld_limit_unlimited').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #description_fld_limit').val('').prop('disabled', true);
            } else {
                $('#skd-pl-price-plan-add-edit #description_fld_limit').prop('disabled', false);
            }
        });

        $('#skd-pl-price-plan-add-edit #images_fld').change(function () {
            $('#skd-pl-price-plan-add-edit #images_fld_limit').val('').prop('disabled', false);
            $('#skd-pl-price-plan-add-edit #images_fld_limit_unlimited').prop('checked', false);
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #images_fld_div').show();
            } else {
                $('#skd-pl-price-plan-add-edit #images_fld_div').hide();
            }
        });
        $('#skd-pl-price-plan-add-edit #images_fld_limit_unlimited').change(function () {
            if ($(this).is(':checked')) {
                $('#skd-pl-price-plan-add-edit #images_fld_limit').val('').prop('disabled', true);
            } else {
                $('#skd-pl-price-plan-add-edit #images_fld_limit').prop('disabled', false);
            }
        });
        //price plan add/edit form
    });
})(jQuery);
