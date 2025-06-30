<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'font-awesome' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION

// === START - PAPEZLEV CUSTOM CODE ===

// 1. Include the custom PHP functions file for the side menu.
//    This file contains the function to register the menu location and the HTML structure.
require_once get_stylesheet_directory() . '/bocni-menu.php';

// 2. Enqueue custom scripts and styles for the side menu.
function papezlev_enqueue_custom_menu_assets() {
    // Enqueue the custom CSS file for the side menu.
    wp_enqueue_style(
        'papezlev-bocni-menu-css',
        get_stylesheet_directory_uri() . '/bocni-menu.css',
        array(), // No dependencies needed for this stylesheet.
        '1.0.0' // Version number, change this to bust cache on updates.
    );

    // Enqueue the custom JavaScript file for the side menu.
    // We add a dependency on 'jquery' and 'wp-i18n' (optional) and place it in the footer.
    wp_enqueue_script(
        'papezlev-bocni-menu-js',
        get_stylesheet_directory_uri() . '/bocni-menu.js',
        array('jquery'), // The script depends on jQuery, so load it first.
        '1.0.0', // Version number, change this to bust cache on updates.
        true // Load the script in the footer.
    );
}
add_action('wp_enqueue_scripts', 'papezlev_enqueue_custom_menu_assets');

// === END - PAPEZLEV CUSTOM CODE ===