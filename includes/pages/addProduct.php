<?php

namespace Webspark_Crud\pages;

use WC_Product_Simple;

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class addProduct
{

    public function __construct()
    {

        add_action('woocommerce_account_add-product_endpoint', [$this, 'addProductForm']);
        add_action('wp_ajax_add_product', [$this, 'saveProduct']);
        add_action('wp_ajax_nopriv_add_product', [$this, 'saveProduct']);
        add_action('wp_enqueue_scripts', [$this, 'addProductScripts']);

    }

    /**
     * Add product form
     *
     * @return void
     */
    public function addProductForm(): void
    {
        $product_id = isset( $_GET['product_id']) ? intval($_GET['product_id'] ) : '';
        $product = $product_id ? wc_get_product( $product_id ) : null;

        if ($product && get_post_field('post_author', $product->get_id()) != get_current_user_id()) {
            echo '<p>' . __( 'You do not have permission to edit this product.', 'webspark' ) . '</p>';
            return;
        }

        $product_name = $product ? $product->get_name() : '';
        $product_price = $product ? $product->get_regular_price() : '';
        $product_quantity = $product ? $product->get_stock_quantity() : '';
        $product_description = $product ? $product->get_description() : '';
        $product_image_id = $product ? $product->get_image_id() : '';
        $product_image_url = $product_image_id ? wp_get_attachment_url( $product_image_id ) : '';
        ?>
        <h2><?php echo $product ? __('Edit Product', 'webspark' ) : __( 'Add Product', 'webspark' ); ?></h2>
        <form id="add-product-form" method="POST">
            <input type="hidden" name="product_id" id="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
            <p class="form-row form-row-wide">
                <label for="product_name"><?php echo __( 'Product Name', 'webspark' ); ?><abbr class="required" title="required">*</abbr></label>
                <span class="woocommerce-input-wrapper">
                    <input type="text" id="product_name" name="product_name" class="input-text" value="<?php echo esc_attr( $product_name ); ?>" required />
                </span>
            </p>
            <p class="form-row form-row-wide">
                <label for="product_price"><?php echo __( 'Price', 'webspark' ); ?><abbr class="required" title="required">*</abbr></label>
                <span class="woocommerce-input-wrapper">
                    <input type="number" id="product_price" name="product_price" class="input-text" step="0.01" value="<?php echo esc_attr( $product_price ); ?>" required />
                </span>
            </p>
            <p class="form-row form-row-wide">
                <label for="product_quantity"><?php echo __( 'Quantity', 'webspark' ); ?><abbr class="required" title="required">*</abbr></label>
                <span class="woocommerce-input-wrapper">
                    <input type="number" id="product_quantity" name="product_quantity" class="input-text" value="<?php echo esc_attr( $product_quantity ); ?>" required />
                </span>
            </p>
            <p class="form-row form-row-wide">
                <label for="product_description"><?php echo __( 'Description', 'webspark' ); ?></label>
                <?php wp_editor( $product_description, 'product_description', [
                    'textarea_name' => 'product_description',
                    'editor_class' => 'form-control',
                    'media_buttons' => false
                ] ); ?>
            </p>
            <p class="form-row form-row-wide">
                <label for="product_image"><?php echo __( 'Product Image', 'webspark' ); ?></label>
                <button type="button" id="upload_image_button" class="woocommerce-Button button wp-element-button"><?php echo $product ? __( 'Update Image', 'webspark' ) : __( 'Upload Image', 'webspark' ); ?></button>
                <input type="hidden" id="product_image" name="product_image" />
                <div id="preview">
                    <?php if ( $product_image_url ): ?>
                        <img src="<?php echo esc_url( $product_image_url ); ?>" />
                    <?php endif; ?>
                </div>
            </p>
            <button type="submit" id="submit_product" class="woocommerce-Button button wp-element-button"><?php echo $product ? __( 'Update', 'webspark' ) : __( 'Add', 'webspark' ); ?></button>
        </form>
        <div id="response"></div>
        <?php
    }

    /**
     * Product saving/edit processing script
     *
     * @return void
     */
    public function addProductScripts(): void
    {

        if (is_account_page()) {
            wp_enqueue_media();
            wp_enqueue_script( 'add-product-script', WEBSPARK_ASSETS . 'assets/js/add-product.js', ['jquery'], WEBSPARK_VERSION, true);
            wp_localize_script( 'add-product-script', 'addProductAjax', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'add_product_nonce' ),
                'title' => __( 'Select a product image', 'webspark' ),
                'button' => __( 'Use this image', 'webspark' ),
                'error' => __( 'There was an error. Please try again.', 'webspark' ),
                'updated' => __( 'The product has been successfully updated!', 'webspark' ),
                'userID' => get_current_user_id(),
            ] );
        }

    }

    /**
     * Save product
     *
     * @return void
     */
    public function saveProduct(): void
    {

        check_ajax_referer( 'add_product_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
        $product_name = sanitize_text_field( $_POST['product_name'] );
        $product_price = floatval( $_POST['product_price'] );
        $product_quantity = intval( $_POST['product_quantity'] );
        $product_description = wp_kses_post( $_POST['product_description'] );
        $product_image = intval( $_POST['product_image'] );
        $manage_stock = 'yes';

        if ( empty( $product_name ) || empty( $product_price ) || empty( $product_quantity ) ) {
            wp_send_json_error( ['message' => __( 'All fields are required.', 'webspark' )] );
        }

        if ( $product_id ) {
            $product = wc_get_product( $product_id );
            if (!$product || get_post_field( 'post_author', $product->get_id()) != get_current_user_id() ) {
                wp_send_json_error(['message' => __('You do not have permission to edit this product.', 'webspark')]);
            }
        } else {
            $product = new WC_Product_Simple();
        }
        $product->set_name( $product_name );
        $product->set_manage_stock( $manage_stock );
        $product->set_regular_price( $product_price );
        $product->set_stock_quantity( $product_quantity );
        $product->set_description( $product_description );

        if ( $product_image ) {
            $product->set_image_id( $product_image );
        }

        $product->set_status( 'pending' );

        $product_id = $product->save();

        $author = get_user_by( 'id', get_current_user_id() );
        $email = WC()->mailer()->emails['webspark-email'];
        $email->sendEmail( $product_id, $author );

        if ( $product_id ) {
            wp_send_json_success( ['message' => __( 'Product successfully added!', 'webspark' )] );
        } else {
            wp_send_json_error( ['message' => __( 'Failed to add a product.', 'webspark' )] );
        }

    }

}