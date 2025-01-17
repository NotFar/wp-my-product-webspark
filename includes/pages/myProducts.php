<?php

namespace Webspark_Crud\pages;

use WP_Query;

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class myProducts
{

    public function __construct()
    {

        add_action('wp_enqueue_scripts', [$this, 'myProductsScript']);
        add_action('woocommerce_account_my-products_endpoint', [$this, 'myProducts']);
        add_action('wp_ajax_delete_product', [$this, 'deleteProduct']);

    }

    /**
     * My product delete script
     *
     * @return void
     */
    public function myProductsScript(): void
    {

        if ( is_account_page() ) {
            wp_enqueue_script('my-products-script', WEBSPARK_ASSETS . 'assets/js/my-products.js', ['jquery'], WEBSPARK_VERSION, true);
            wp_localize_script( 'my-products-script', 'myProducts', [
                'confirm' => __( 'Are you sure you want to delete this product?', 'webspark' ),
                'alert' => __( 'An error occurred while deleting the product. Please try again.', 'webspark' ),
            ] );
        }

    }

    /**
     * My products table and pagination
     *
     * @return void
     */
    public function myProducts(): void
    {

        global $wp;

        $paged = 1;

        if ( isset( $wp->query_vars['my-products'] ) ) {
            $parts = explode( '/', $wp->query_vars['my-products'] );
            if ( count( $parts ) > 1 && $parts[0] === 'page' && is_numeric( $parts[1] ) ) {
                $paged = (int) $parts[1];
            }
        }

        $current_user_id = get_current_user_id();
        $args = [
            'post_type' => 'product',
            'post_status' => ['publish', 'pending'],
            'author' => $current_user_id,
            'posts_per_page' => 10,
            'paged' => $paged
        ];

        $products = new WP_Query( $args );

        echo '<h2>' . __( 'My Products', 'webspark' ) . '</h2>';
        if ( $products->have_posts() ) {
            echo '<table class="woocommerce-table woocommerce-orders-table shop_table">';
            echo '<thead>
                    <tr>
                        <th class="woocommerce-orders-table__header">' . __( 'Product Name', 'webspark' ) . '</th>
                        <th class="woocommerce-orders-table__header">' . __( 'Quantity', 'webspark' ) . '</th>
                        <th class="woocommerce-orders-table__header">' . __( 'Price', 'webspark' ) . '</th>
                        <th class="woocommerce-orders-table__header">' . __( 'Status', 'webspark' ) . '</th>
                        <th class="woocommerce-orders-table__header">' . __( 'Edit', 'webspark' ) . '</th>
                        <th class="woocommerce-orders-table__header">' . __( 'Delete', 'webspark' ) . '</th>
                    </tr>
                  </thead><tbody>';

            while ( $products->have_posts() ) {
                $products->the_post();
                $product = wc_get_product(get_the_ID());

                echo '<tr>
                        <td>' . esc_html($product->get_name()) . '</td>
                        <td>' . esc_html($product->get_stock_quantity()) . '</td>
                        <td>' . wc_price($product->get_price()) . '</td>
                        <td>' . esc_html(get_post_status(get_the_ID())) . '</td>
                        <td><a href="' . esc_url(add_query_arg('product_id', get_the_ID(), wc_get_account_endpoint_url('add-product'))) . '" class="button">' . __('Edit', 'webspark') . '</a></td>
                        <td><button class="delete-product-button woocommerce-button wp-element-button button" data-product-id="' . get_the_ID() . '">' . __('Delete', 'webspark') . '</button></td>
                      </tr>';
            }

            echo '</tbody></table>';

            echo paginate_links( [
                'base' => trailingslashit( wc_get_account_endpoint_url( 'my-products' ) ) . 'page/%#%/',
                'format' => '',
                'total' => $products->max_num_pages,
                'current' => $paged,
                'prev_text' => __( '&laquo; Previous', 'webspark' ),
                'next_text' => __( 'Next &raquo;', 'webspark' ),
            ] );
        } else {
            echo '<p>' . __( 'No products found.', 'webspark' ) . '</p>';
        }

        wp_reset_postdata();

    }

    /**
     * Delete product function
     *
     * @return void
     */
    public function deleteProduct(): void
    {

        check_ajax_referer( 'add_product_nonce', 'nonce' );

        $product_id = intval( $_POST['product_id'] );
        if ( !$product_id || get_post_field( 'post_author', $product_id) != get_current_user_id() ) {
            wp_send_json_error( ['message' => __( 'You do not have permission to delete this product.', 'webspark' )] );
        }

        if ( wp_trash_post( $product_id ) ) {
            wp_send_json_success( ['message' => __( 'Product deleted successfully.', 'webspark' )] );
        } else {
            wp_send_json_error( ['message' => __( 'Failed to delete product.', 'webspark' )] );
        }

    }

}