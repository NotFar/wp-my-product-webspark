<?php
/*
Plugin Name: Webspark CRUD for Woocommerce
Plugin URI: https://www.webspark.ua/
Description: CRUD operations for working with products through the "My account" page
Version: 1.0.0
Author: webspark
License: GPL v3 or later
Text Domain: webspark
Domain Path: /languages
Author URI: https://www.webspark.ua/
*/

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Create constants and initializes the plugin's
 *
 * @return void
 */
add_action( 'plugins_loaded', 'websparkLoaded', 99 );
function websparkLoaded(): void
{

    // Define the current version of the plugin.
    define( 'WEBSPARK_VERSION', '1.0.0' );

    // If active plugin.
    define( 'WEBSPARK_ACTIVE', true );

    // Define a constant for the plugin file path.
    define( 'WEBSPARK__FILE', __FILE__ );

    // Define the url to the plugin's directory.
    define( 'WEBSPARK_ASSETS', plugin_dir_url(__FILE__) );

    // Define the absolute path to the plugin's directory.
    define( 'WEBSPARK_PATH', plugin_dir_path( WEBSPARK__FILE ) );

    // Define a base for the plugin, used for generating links to the plugin's pages.
    define( 'WEBSPARK_PLUGIN_BASE', plugin_basename( WEBSPARK__FILE ) );

    // Load plugin text domains
    load_plugin_textdomain('webspark', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    // Include the main plugin file that initializes the plugin's classes and functionality.
    require WEBSPARK_PATH . 'includes/webspark.php';

}

/**
 * Flush rewrite rules important(сalled once when the plugin is activated)
 *
 * @return void
 */
function rewriteRules(): void
{

    add_rewrite_endpoint('my-products', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('add-product', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();

}
register_activation_hook(__FILE__, 'rewriteRules');