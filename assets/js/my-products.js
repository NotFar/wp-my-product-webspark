jQuery(document).ready(function ($) {
    $('.delete-product-button').on('click', function (e) {
        e.preventDefault();

        if (!confirm(myProducts.confirm)) {
            return;
        }

        const productId = $(this).data('product-id');

        $.ajax({
            url: addProductAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_product',
                product_id: productId,
                nonce: addProductAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function () {
                alert(myProducts.alert);
            }
        });
    });
});