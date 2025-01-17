jQuery(document).ready(function ($) {
    let mediaUploader;

    $('#upload_image_button').on('click', function (e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: addProductAjax.title,
            button: {
                text: addProductAjax.button
            },
            library: {
                type: 'image',
                author: addProductAjax.userID
            },
            multiple: false
        });

        mediaUploader.on('select', function () {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#product_image').val(attachment.id);
            $('#preview').html('<img src="' + attachment.url + '"/>');
        });

        mediaUploader.on('error', function (error) {
            console.error('Error uploading media:', error);
            alert('An error occurred while uploading. Please try again.');
        });

        mediaUploader.open();
    });

    $('#add-product-form').on('submit', function (e) {
        e.preventDefault();

        let formData = $(this).serialize();
        formData += '&action=add_product&nonce=' + addProductAjax.nonce;

        $('#submit_product').attr('disabled', true);
        $('#response').html('');

        $.ajax({
            url: addProductAjax.ajax_url,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#response').html('<div style="color: green;">' + addProductAjax.updated + '</div>');
                    if (!$('#product_id').val()) {
                        $('#add-product-form')[0].reset();
                        $('#preview').html('');
                        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
                            tinyMCE.activeEditor.setContent('');
                        }
                        $('#response').html('<div style="color: green;">' + response.data.message + '</div>');
                    }
                } else {
                    $('#response').html('<div style="color: red;">' + response.data.message + '</div>');
                }
            },
            error: function () {
                $('#response').html('<div style="color: red;">' + addProductAjax.error + '</div>');
            },
            complete: function () {
                $('#submit_product').attr('disabled', false);
            }
        });
    });
});