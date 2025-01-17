<?php

namespace Webspark_Crud;

use Webspark_Crud\pages\addProduct;
use Webspark_Crud\pages\myProducts;
use Webspark_Crud\emails\registerEmail;

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class pluginWebspark
{

    /**
     * Instance.
     *
     * Holds the plugin instance.
     *
     */
    public static $instance = null;

    /**
     * Plugin settings instance
     *
     */
    public pluginSettings $settings;

    /**
     * Add product instance
     *
     */
    public addProduct $addProduct;

    /**
     * My products instance
     *
     */
    public myProducts $myProducts;

    /**
     * Register email instance
     *
     */
    public registerEmail $registerEmail;


    /**
     * Instance.
     *
     * Ensures only one instance of the plugin class is loaded or can be loaded.
     *
     * @return pluginWebspark|null
     */
    public static function instance(): ?pluginWebspark
    {

        if ( is_null( self::$instance ) ) {

            self::$instance = new self();

        }

        return self::$instance;

    }

    /**
     * Register autoloader.
     *
     * @return void
     */
    private function registerAutoloader(): void
    {
        require WEBSPARK_PATH . 'includes/autoloader.php';
        autoloader::run();
    }

    private function __construct()
    {

        $this->registerAutoloader();
        add_action( 'after_setup_theme', [ $this, 'initComponents' ] );

    }

    /**
     * Initializes plugin components.
     *
     * handler with it, so that all plugin functionality is ready to use.
     *
     * @return void
     */
    public function initComponents(): void
    {

        $this->settings = new pluginSettings();
        $this->addProduct = new addProduct();
        $this->myProducts = new myProducts();
        $this->registerEmail = new registerEmail();

    }

}

pluginWebspark::instance();