<?php
/**
 * Hlavní soubor s funkcemi pro child šablonu MinimalistBlogger.
 * Načítá ostatní soubory s funkcemi pro lepší přehlednost.
 */

// Zabráníme přímému přístupu
if ( ! defined( 'ABSPATH' ) ) exit;

// Adresář s našimi rozdělenými soubory
$inc_dir = get_stylesheet_directory() . '/inc';

// Načtení všech potřebných souborů s českými názvy
require_once( $inc_dir . '/nacitani-skriptu.php' );
require_once( $inc_dir . '/nastaveni-sablony.php' );
require_once( $inc_dir . '/vlastni-prispevky.php' );
require_once( $inc_dir . '/nastaveni-administrace.php' );
require_once( $inc_dir . '/nastaveni-youtube.php' );
require_once( $inc_dir . '/nastaveni-radia.php' );
require_once( $inc_dir . '/nastaveni-navodu.php' );

// === PŘIDANÝ ŘÁDEK ZDE ===
require_once( $inc_dir . '/nastaveni-vzhledu-home.php' ); // Načtení logiky pro řazení na Home page

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
 * Načtení stylů a skriptů pro šablonu stránky "Fotogalerie".
 */
function enqueue_fotogalerie_assets() {
    // Načteme styly a skripty, pouze pokud je zobrazena stránka s naší novou šablonou
    if ( is_page_template( 'page-fotogalerie.php' ) ) {
        $theme_version = wp_get_theme()->get('Version');

        // Načtení CSS pro Lightbox2 z CDN
        wp_enqueue_style(
            'lightbox-css',
            'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css',
            array(),
            '2.11.3'
        );

        // Načtení vlastních stylů pro galerii
        wp_enqueue_style(
            'page-fotogalerie-style',
            get_stylesheet_directory_uri() . '/css/page-fotogalerie.css',
            array('lightbox-css'), // Načte se až po stylech lightboxu
            $theme_version
        );

        // Načtení JS pro Lightbox2 z CDN (závisí na jQuery)
        wp_enqueue_script(
            'lightbox-js',
            'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js',
            array('jquery'),
            '2.11.3',
            true // Načíst v patičce
        );

        // Načtení našeho (zatím prázdného) JS souboru
        wp_enqueue_script(
            'page-fotogalerie-script',
            get_stylesheet_directory_uri() . '/js/page-fotogalerie.js',
            array('lightbox-js'), // Načte se až po lightboxu
            $theme_version,
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_fotogalerie_assets' );