<?php

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Plain text content emil.
 *
 * @var string $product_title
 * @var string $author_url
 * @var string $edit_url
 */

echo strtoupper( __( 'A new product has been added or edited.', 'webspark' ) ) . "\n\n";

echo __( 'Product Title:', 'webspark' ) . ' ' . $product_title . "\n";
echo __( 'Author Profile URL:', 'webspark' ) . ' ' . $author_url . "\n";
echo __( 'Edit Product URL:', 'webspark' ) . ' ' . $edit_url . "\n";