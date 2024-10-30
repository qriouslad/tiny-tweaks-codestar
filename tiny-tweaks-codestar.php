<?php
/*
 * Plugin Name:       Tiny Tweaks - Codestar
 * Plugin URI:        https://github.com/qriouslad/tiny-tweaks-codestar
 * Description:       Tiny tweaks for WordPress
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Bowo
 * Author URI:        https://bowo.io/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/qriouslad/tiny-tweaks-codestar
 * Text Domain:       tiny-tweaks-codestar
 * Domain Path:       /languages
 * Requires Plugins:  
 */

// Prevent direct access to the PHP file, without going through WordPress where 
// the constant ABSPATH is defined (in wp-config.php)
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request' );
}

define( 'TTC_VERSION', '1.0.0' );



// ========== Load Codestart framework to create settings page ==========

if ( ! class_exists( 'CSF' ) ) {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/codestar-framework/codestar-framework.php';    
}



// ========== Create settings page using Codestar framework ==========

add_action( 'csf_loaded', 'ttc_settings_page' );

function ttc_settings_page() {

    // Set a unique slug-like ID
    $prefix = 'ttc_tiny_tweaks';

    // Create options
    CSF::createOptions( $prefix, array(
        'menu_title' => 'Tiny Tweaks',
        'menu_slug'  => 'ttc-tiny-tweaks',
        'menu_type'         => 'submenu',
        'menu_parent'       => 'options-general.php',
        'menu_position'     => 100,
        'theme'             => 'light',
        'framework_title'   => 'Tiny Tweaks',
        'framework_class'   => 'ttc-options',
        'show_bar_menu'     => false,
        'show_search'       => false,
        'show_reset_all'    => false,
        'show_reset_section' => false,
        'show_form_warning' => false,
        'save_defaults'     => true,
        'show_footer'       => false,
        'footer_credit'     => '',
    ) );

    // Create a section
    CSF::createSection( $prefix, array(
        'title'  => 'Settings',
        'fields' => array(

            array(
              'id'          => 'admin_footer',
              'type'        => 'fieldset',
              'title'       => 'Admin Footer',
              'subtitle'    => 'Make simple modifications to the left-side credits text and the right-side WordPress version number.',
              'fields'      => array(

                    // Add a text field
                    array(
                        'id'    => 'admin_footer_left_text',
                        'type'  => 'text',
                        'title' => 'Left-Side Text',
                    ),

                    // Add a checkbox field
                    array(
                        'id'      => 'admin_footer_hide_wp_version',
                        'type'    => 'checkbox',
                        'title'   => 'WordPress Version Number',
                        'label'   => 'Let\'s hide it',
                        'default' => false
                    ),
              
              ),
            ),


            array(
              'id'          => 'login_page',
              'type'        => 'fieldset',
              'title'       => 'Login Page',
              'subtitle'    => 'Make simple modifications to the login page.',
              'fields'      => array(

                    // Add a switcher field
                    array(
                        'id'       => 'login_page_use_site_icon',
                        'type'     => 'switcher',
                        'title'    => 'Use Site Icon as Logo',
                        'label'    => 'If Site Icon is set in "Settings >> General", let\'s use it to replace the default WordPress logo',
                        'text_on'  => 'Yes',
                        'text_off' => 'No',
                    ),
                                  
              ),
            ),

        )
    ) );

}



// ========== Change left-side footer credits ==========

add_filter( 'admin_footer_text', 'ttsa_return_new_admin_footer_text' );

function ttsa_return_new_admin_footer_text( $text ) {
    $options = get_option( 'ttc_tiny_tweaks' );
    $text = isset( $options['admin_footer']['admin_footer_left_text'] ) ? $options['admin_footer']['admin_footer_left_text'] : $text;

    return $text;
}



// ========== Remove right-side footer WP version number ==========

add_filter( 'update_footer', 'ttsa_remove_footer_wp_version', 20 );

function ttsa_remove_footer_wp_version( $content ) {
    $options = get_option( 'ttc_tiny_tweaks' );
    $hide_wp_version_number = isset( $options['admin_footer']['admin_footer_hide_wp_version'] ) ? $options['admin_footer']['admin_footer_hide_wp_version'] : false;

    if ( $hide_wp_version_number ) {
        echo ''; // Output nothing
    } else {
        echo $content;
    }
}



// ========== Use site icon for login page logo ==========

add_action( 'login_head', 'ttsa_use_site_icon_as_login_page_logo' );

function ttsa_use_site_icon_as_login_page_logo() {
    $options = get_option( 'ttc_tiny_tweaks' );
    $login_page_use_site_icon = isset( $options['login_page']['login_page_use_site_icon'] ) ? $options['login_page']['login_page_use_site_icon'] : false;

    if ( has_site_icon() && $login_page_use_site_icon ) { 
        ?>
        <style type="text/css">
                .login h1 a {
                        background-image: url('<?php site_icon_url( 180 ); ?>');
                }
        </style>
        <?php
    }
}