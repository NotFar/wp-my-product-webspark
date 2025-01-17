<?php

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Email content with table.
 *
 * @var string $product_title
 * @var string $author_url
 * @var string $edit_url
 * @var string $email_heading
 * @var WC_Email $email
 */

do_action( 'woocommerce_email_header', $email_heading, $email );

echo '<h3>' . esc_html__( 'A new product has been added or edited.', 'webspark' ) . '</h3>';

echo '<table cellspacing="0" cellpadding="6" border="1" style="width: 100%; border: 1px solid #e5e5e5; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; color: #333;">';
    echo '<tbody>';
        echo '<tr>';
            echo '<th style="text-align: left; padding: 12px; border: 1px solid #e5e5e5; background-color: #f9f9f9;">';
                echo esc_html__( 'Product Title', 'webspark' );
            echo '</th>';
            echo '<td style="padding: 12px; border: 1px solid #e5e5e5;">';
                echo esc_html( $product_title );
            echo '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<th style="text-align: left; padding: 12px; border: 1px solid #e5e5e5; background-color: #f9f9f9;">';
                echo esc_html__( 'Author Profile URL', 'webspark' );
            echo '</th>';
            echo '<td style="padding: 12px; border: 1px solid #e5e5e5;">';
                echo '<a href="' . esc_url( $author_url ) . '" style="color: #0073aa;">' . esc_html( $author_url ) . '</a>';
            echo '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<th style="text-align: left; padding: 12px; border: 1px solid #e5e5e5; background-color: #f9f9f9;">';
                echo esc_html__( 'Edit Product URL', 'webspark' );
            echo '</th>';
            echo '<td style="padding: 12px; border: 1px solid #e5e5e5;">';
                echo '<a href="' . esc_url( $edit_url ) . '" style="color: #0073aa;">' . esc_html( $edit_url ) . '</a>';
            echo '</td>';
        echo '</tr>';
    echo '</tbody>';
echo '</table>';

do_action( 'woocommerce_email_footer', $email );
