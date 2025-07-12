<?php
/**
 * Hlavní soubor s funkcemi pro child šablonu MinimalistBlogger.
 * Načítá ostatní soubory s funkcemi pro lepší přehlednost.
 */

// Zabráníme přímému přístupu
if ( ! defined( 'ABSPATH' ) ) exit;

// Adresář s našimi rozdělenými soubory
$inc_dir = get_stylesheet_directory() . '/inc';

// Načtení všech potřebných souborů
require_once( $inc_dir . '/nacitani-skriptu.php' );
require_once( $inc_dir . '/nastaveni-sablony.php' );
require_once( $inc_dir . '/vlastni-prispevky.php' );
require_once( $inc_dir . '/nastaveni-administrace.php' ); // Obsahuje všechna nastavení menu
require_once( $inc_dir . '/nastaveni-youtube.php' );
require_once( $inc_dir . '/nastaveni-radia.php' );
require_once( $inc_dir . '/nastaveni-navodu.php' );

// Načtení logiky pro odesílání e-mailů (pokud existuje)
if ( file_exists( get_stylesheet_directory() . '/ecomail-sender.php' ) ) {
    require_once( get_stylesheet_directory() . '/ecomail-sender.php' );
}

// Původní kód z rodičovské šablony (důležité pro funkčnost)
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

/**
 * Načtení stylů a skriptů pro Lightbox2
 */
function enqueue_fotogalerie_assets() {
    // Načteme styly a skripty, pokud je zobrazena stránka FOTOGALERIE nebo HOME.
    if ( is_page_template( 'page-fotogalerie.php' ) || is_page_template( 'page-home.php' ) ) {
        $theme_version = wp_get_theme()->get('Version');

        // Načtení CSS pro Lightbox2 z CDN
        wp_enqueue_style(
            'lightbox-css',
            'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css',
            array(),
            '2.11.3'
        );

        // Načtení vlastních stylů pro galerii (pokud je stránka fotogalerie)
        if ( is_page_template( 'page-fotogalerie.php' ) ) {
            wp_enqueue_style(
                'page-fotogalerie-style',
                get_stylesheet_directory_uri() . '/css/page-fotogalerie.css',
                array('lightbox-css'),
                $theme_version
            );
        }

        // Načtení JS pro Lightbox2 z CDN (závisí na jQuery)
        wp_enqueue_script(
            'lightbox-js',
            'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js',
            array('jquery'),
            '2.11.3',
            true // Načíst v patičce
        );

        // Načtení našeho JS souboru pro fotogalerii (pokud existuje a je potřeba)
        if ( is_page_template( 'page-fotogalerie.php' ) ) {
            wp_enqueue_script(
                'page-fotogalerie-script',
                get_stylesheet_directory_uri() . '/js/page-fotogalerie.js',
                array('lightbox-js'),
                $theme_version,
                true
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_fotogalerie_assets' );

/**
 * Načtení stylů pro stránku Nastavení vzhledu.
 */
function enqueue_nastaveni_vzhledu_assets() {
    // Načteme styly pouze, pokud je zobrazena stránka s šablonou "page-nastaveni-vzhledu.php".
    if ( is_page_template( 'page-nastaveni-vzhledu.php' ) ) {
        wp_enqueue_style(
            'page-nastaveni-vzhledu-style',
            get_stylesheet_directory_uri() . '/css/page-nastaveni-vzhledu.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_nastaveni_vzhledu_assets' );

/**
 * Vloží do hlavičky stránky inline CSS pro přepsání barev dolní lišty.
 */
function pehobr_output_bottom_nav_colors_css() {
    // Načtení uložených hodnot s výchozími barvami
    $bg_color = get_option('pehobr_bottom_nav_bg_color', '#7e7383');
    $icon_color = get_option('pehobr_bottom_nav_icon_color', '#ffffff');
    $convex_bg_color = get_option('pehobr_bottom_nav_convex_bg_color', '#ffffff');
    $active_icon_color = get_option('pehobr_bottom_nav_active_icon_color', '#7e7383');

    // Sestavení CSS pouze pokud se hodnoty liší od výchozích
    if ($bg_color !== '#7e7383' || $icon_color !== '#ffffff' || $convex_bg_color !== '#ffffff' || $active_icon_color !== '#7e7383') {
        $custom_css = "
            :root {
                --nav-bg-color: " . esc_attr($bg_color) . ";
                --nav-border-color: " . esc_attr($icon_color) . ";
                --nav-convex-bg: " . esc_attr($convex_bg_color) . ";
                --nav-active-color: " . esc_attr($active_icon_color) . ";
            }
        ";
        echo '<style type="text/css" id="custom-bottom-nav-colors">' . preg_replace( '/\s+/', ' ', $custom_css ) . '</style>';
    }
}
add_action('wp_head', 'pehobr_output_bottom_nav_colors_css');