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

// === PŘIDANÝ ŘÁDEK ZDE ===
require_once( $inc_dir . '/nastaveni-radia.php' );
// Načtení nastavení pro řazení návodů
require_once get_stylesheet_directory() . '/inc/nastaveni-navodu.php';

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