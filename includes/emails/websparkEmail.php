<?php

namespace Webspark_Crud\emails;

use WC_Email;

// Prevent direct access to the file.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class websparkEmail extends WC_Email
{

    public function __construct()
    {

        $this->id = 'webspark-email';
        $this->title = __( 'Webspark Email', 'webspark' );
        $this->description = __( 'This is an email from webspark that is sent after a user edits/adds a product.', 'webspark' );
        $this->enabled = 'yes';
        $this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );

        $this->template_html  = 'emails/webspark-email.php';
        $this->template_plain = 'emails/plain/webspark-email.php';

        $this->subject = __( 'A new product has been added or edited.', 'webspark' );
        $this->heading = __( 'A new product has been added or edited.', 'webspark' );

        parent::__construct();

    }


    /**
     * Initialization of form fields
     *
     * @return void
     */
    public function init_form_fields(): void
    {
        $this->form_fields = [
            'enabled' => [
                'title'   => __( 'Enable/Disable', 'woocommerce' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable this email notification.', 'woocommerce' ),
                'default' => 'yes',
            ],
            'email_type' => [
                'title'   => __( 'Email Type', 'woocommerce' ),
                'type'    => 'select',
                'description' => __( 'Choose the email format.', 'woocommerce' ),
                'options' => [
                    'html' => __( 'HTML', 'woocommerce' ),
                    'plain' => __( 'Plain Text', 'woocommerce' ),
                ],
                'default' => 'html',
            ],
            'recipient' => [
                'title'       => __( 'Recipient(s)', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'Enter recipients (comma-separated). Defaults to admin email.', 'woocommerce' ),
                'placeholder' => '',
                'default'     => get_option( 'admin_email' ),
            ],
        ];
    }

    /**
     * Send email function
     *
     * @param $product_id
     * @param $author
     * @return void
     */
    public function sendEmail( $product_id, $author ): void
    {
        if ( ! $this->is_enabled() ) {
            return;
        }

        if ( ! $this->get_recipient() ) {
            return;
        }

        $this->object = get_post( $product_id );

        $this->placeholders = [
            '{product_title}' => $this->object->post_title,
            '{author_name}'   => $author->display_name,
            '{author_url}'    => admin_url( "user-edit.php?user_id={$author->ID}" ),
            '{edit_url}'      => admin_url( "post.php?post={$product_id}&action=edit" ),
        ];

        $email_type = $this->get_option( 'email_type', 'html' );

        if ( 'html' === $email_type ) {
            $this->send(
                $this->get_recipient(),
                $this->get_subject(),
                $this->emailHtml(),
                $this->get_headers(),
                $this->get_attachments()
            );
        } else {
            $this->send(
                $this->get_recipient(),
                $this->get_subject(),
                $this->emailPlain(),
                $this->get_headers(),
                $this->get_attachments()
            );
        }

    }

    /**
     * Email content html
     *
     * @return bool|string
     */
    public function emailHtml(): bool|string
    {
        ob_start();
        wc_get_template(
            $this->template_html,
            [
                'product_title'  => $this->placeholders['{product_title}'],
                'author_url'     => $this->placeholders['{author_url}'],
                'edit_url'       => $this->placeholders['{edit_url}'],
                'email_heading'  => $this->get_heading(),
                'email'          => $this,
            ]
        );
        return ob_get_clean();
    }

    /**
     * Email content plain
     *
     * @return bool|string
     */
    public function emailPlain(): bool|string
    {
        ob_start();
        wc_get_template(
            $this->template_plain,
            [
                'product_title' => $this->placeholders['{product_title}'],
                'author_url'    => $this->placeholders['{author_url}'],
                'edit_url'      => $this->placeholders['{edit_url}'],
            ]
        );
        return ob_get_clean();
    }
}