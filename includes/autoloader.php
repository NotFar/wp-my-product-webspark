<?php

namespace Webspark_Crud;

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class autoloader
{

    /**
     * Run autoloader.
     *
     * Register a function as `__autoload()` implementation.
     *
     * @return void
     */
    public static function run(): void
    {

        spl_autoload_register( array(
            __CLASS__,
            'autoload',
        ) );

    }

    /**
     * Load class.
     * For a given class name, require the class file.
     *
     * @param $class_name
     * @return void
     */
    private static function loadClass( $class_name ): void
    {

        $file     = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );
        $file     = str_replace( '_', '-', $file );
        $filepath = WEBSPARK_PATH . 'includes/' . $file . '.php';

        if ( is_readable( $filepath ) ) {
            require $filepath;
        }

    }


    /**
     * Autoload.
     *
     * @param $class_name
     * @return void
     */
    private static function autoload( $class_name ): void
    {

        if ( 0 !== strpos( $class_name, __NAMESPACE__ . '\\' ) ) {
            return;
        }

        $relative_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\/', '', $class_name );
        $final_class_name    = __NAMESPACE__ . '\\' . $relative_class_name;

        if ( ! class_exists( $final_class_name ) ) {
            self::loadClass( $relative_class_name );
        }

    }

}