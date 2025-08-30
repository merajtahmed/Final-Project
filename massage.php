<?php
/**
 * Plugin Name: Simple Message Generator
 * Description: Type a custom message in admin and display it using [view_message] shortcode.
 * Version: 1.1
 * Author: Your Name
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Simple_Message_Generator {
    private $option_key = 'smg_message';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_shortcode( 'view_message', [ $this, 'display_message' ] );
    }

    // Create a menu item in sidebar
    public function add_settings_page() {
        add_menu_page(
            'Simple Message',           // Page title
            'Simple Message',           // Menu title in sidebar
            'manage_options',           // Capability
            'smg-settings',             // Menu slug
            [ $this, 'render_settings_page' ], // Callback
            'dashicons-format-chat',    // Icon
            20                          // Position
        );
    }

    public function register_settings() {
        register_setting( 'smg_settings_group', $this->option_key, 'sanitize_text_field' );

        add_settings_section(
            'smg_main',
            'Message Settings',
            function() { echo '<p>Type your message below.</p>'; },
            'smg-settings'
        );

        add_settings_field(
            'smg_message',
            'Your Message',
            function() {
                $value = get_option( $this->option_key, '' );
                echo '<textarea name="'.esc_attr( $this->option_key ).'" rows="3" class="large-text">'.esc_textarea( $value ).'</textarea>';
            },
            'smg-settings',
            'smg_main'
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Simple Message Generator</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'smg_settings_group' );
                do_settings_sections( 'smg-settings' );
                submit_button();
                ?>
            </form>
            <h2>Preview</h2>
            <div style="padding:10px;background:#f1f1f1;border-radius:6px;">
                <?php echo $this->display_message(); ?>
            </div>
            <p><strong>Use this shortcode to display the message:</strong> <code>[view_message]</code></p>
        </div>
        <?php
    }

    public function display_message() {
        $msg = get_option( $this->option_key, '' );
        if ( ! empty( $msg ) ) {
            return '<div class="smg-message">'.esc_html( $msg ).'</div>';
        }
        return '';
    }
}

new Simple_Message_Generator();
