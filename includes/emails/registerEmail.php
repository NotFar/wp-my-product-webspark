<?php

namespace Webspark_Crud\emails;

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class registerEmail
{

    public function __construct()
    {

        add_filter( 'woocommerce_email_classes', [ $this, 'registerWebsparkEmail' ] );

        add_filter( 'woocommerce_locate_template', function( $template, $template_name, $template_path ) {
            $plugin_path = WEBSPARK_PATH . 'templates/';
            if ( file_exists( $plugin_path . $template_name ) ) {
                $template = $plugin_path . $template_name;
            }
            return $template;
        }, 10, 3 );

    }

    /**
     * Email registration.
     *
     * @param $email_classes
     * @return mixed
     */
    public function registerWebsparkEmail( $email_classes ): mixed
    {

        require_once WEBSPARK_PATH . 'includes/emails/websparkEmail.php';
        $email_classes['webspark-email'] = new websparkEmail();
        return $email_classes;

    }

}