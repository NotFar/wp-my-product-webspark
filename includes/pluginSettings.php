<?php

namespace Webspark_Crud;

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class pluginSettings
{

    public function __construct()
    {

        add_filter( 'admin_notices', [ $this, 'pluginNotice' ] );
        add_filter( 'woocommerce_account_menu_items', [ $this, 'addMenuLinks' ] );
        add_action( 'init', [ $this, 'linksEndpoints' ] );
        add_filter( 'woocommerce_account_menu_items', [ $this, 'menuLinksReorder' ] );

    }

    /**
     * Check if the Woocommerce plugin is activated
     *
     * @return void
     */
    public function pluginNotice(): void
    {

        if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            echo '<div class="error"><p>' . __( 'Important! For the "Webspark CRUD for Woocommerce" plugin to work, install and activate the "Woocommerce" plugin.', 'webspark' ) . '</p></div>';
            deactivate_plugins( WEBSPARK_PLUGIN_BASE );
        }

    }

    /**
     * Add the "Add Product" and "My Products" menu tabs to the "My Account" page
     *
     * @param $menu_links
     */
    public function addMenuLinks( $menu_links )
    {

        $menu_links['add-product'] = __( 'Add product', 'webspark' );
        $menu_links['my-products'] = __( 'My products', 'webspark' );

        return $menu_links;

    }

    /**
     * Register endpoints
     * @return void
     */
    public function linksEndpoints(): void
    {

        add_rewrite_endpoint( 'add-product', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'my-products', EP_ROOT | EP_PAGES );

    }

    /**
     * Reorder menu links
     *
     * @param $menu_links
     * @return array
     */
    public function menuLinksReorder( $menu_links ): array
    {

        return array(
            'dashboard' => __( 'Dashboard', 'woocommerce' ),
            'orders' => __( 'Orders', 'woocommerce' ),
            'downloads' => __( 'Downloads', 'woocommerce' ),
            'edit-address' => __( 'Addresses', 'woocommerce' ),
            'edit-account' => __( 'Account details', 'woocommerce' ),
            'add-product' => __( 'Add product', 'webspark' ),
            'my-products' => __( 'My products', 'webspark' ),
            'customer-logout' => __( 'Logout', 'woocommerce' )
        );

    }

}