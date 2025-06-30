<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Načtení souboru s logikou pro boční menu
require_once( get_stylesheet_directory() . '/bocni-menu.php' );

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

/**
 * Načte styly a skripty pro vlastní boční menu.
 */
function moje_vlastni_menu_assets() {
    // Načtení CSS pro boční menu
    wp_enqueue_style(
        'moje-bocni-menu-style',
        get_stylesheet_directory_uri() . '/style/bocni-menu.css',
        array(), // Závislosti, pokud nějaké jsou
        filemtime(get_stylesheet_directory() . '/style/bocni-menu.css') // Verze souboru pro invalidaci cache
    );

    // Načtení JavaScriptu pro boční menu
    wp_enqueue_script(
        'moje-bocni-menu-script',
        get_stylesheet_directory_uri() . '/js/bocni-menu.js',
        array(), // Závislosti, např. 'jquery'
        filemtime(get_stylesheet_directory() . '/js/bocni-menu.js'), // Verze souboru
        true // Načíst ve patičce stránky
    );
}
add_action( 'wp_enqueue_scripts', 'moje_vlastni_menu_assets' );

?>