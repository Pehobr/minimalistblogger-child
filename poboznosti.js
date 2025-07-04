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

/**
 * Načtení všech vlastních stylů a skriptů pro child šablonu.
 */
function minimalistblogger_child_enqueue_assets() {
    $theme_version = wp_get_theme()->get('Version');

    // --- Načtení CSS stylů ---
    // Základní styly, které zůstávají
    wp_enqueue_style( 'minimalistblogger-dolni-lista', get_stylesheet_directory_uri() . '/css/dolni-lista.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-fixni-hlavicka', get_stylesheet_directory_uri() . '/css/fixni-hlavicka.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-prispevky', get_stylesheet_directory_uri() . '/css/prispevky.css', array('chld_thm_cfg_parent'), $theme_version );

    // --- Načtení NOVĚ ORGANIZOVANÝCH STYLŮ ---
    // Styl pro mobilní zařízení se načte vždy
    wp_enqueue_style( 'minimalistblogger-vzhled-mobil', get_stylesheet_directory_uri() . '/css/vzhled-mobil.css', array('chld_thm_cfg_parent'), $theme_version );
    
    // Styl pro PC se načte jen pro obrazovky širší než 992px
    wp_enqueue_style( 'minimalistblogger-vzhled-pc', get_stylesheet_directory_uri() . '/css/vzhled-pc.css', array('chld_thm_cfg_parent'), $theme_version, 'screen and (min-width: 992px)' );

    // --- Načtení JavaScriptu ---
    wp_enqueue_script(
        'minimalistblogger-dolni-lista-js', // Unikátní název skriptu
        get_stylesheet_directory_uri() . '/js/dolni-lista.js', // Cesta k souboru
        array(), // Závislosti
        $theme_version, // Verze
        true // Načíst v patičce
    );
}
add_action( 'wp_enqueue_scripts', 'minimalistblogger_child_enqueue_assets', 20 );

/**
 * Načtení specifických stylů a skriptů pro šablonu "Aplikace Liturgické Čtení"
 * A také pro jednotlivé příspěvky z rubriky "liturgicke-cteni".
 */
function moje_aplikace_assets() {
    $theme_version = wp_get_theme()->get('Version');

    // Sjednocená podmínka: Načíst na stránce s aplikací NEBO na detailu příspěvku z dané rubriky
    $is_liturgicka_stranka = is_page_template('page-liturgicke-cteni.php') || (is_singular('post') && has_category('liturgicke-cteni'));

    if ( $is_liturgicka_stranka ) {
        
        // Načtení CSS souboru
        wp_enqueue_style( 
            'liturgicke-cteni-style', 
            get_stylesheet_directory_uri() . '/css/liturgicke-cteni.css', 
            array(),
            $theme_version 
        );

        // Načtení JavaScriptového souboru
        wp_enqueue_script( 
            'liturgicke-cteni-script', 
            get_stylesheet_directory_uri() . '/js/liturgicke-cteni.js', 
            array('jquery'),
            $theme_version, 
            true
        );

        // Data nyní předáváme přímo ze šablony `page-liturgicke-cteni.php`.
        // Zde necháme pouze logiku pro PŘÍMÉ zobrazení příspěvku.
        if ( is_singular('post') && has_category('liturgicke-cteni') ) {
            global $post;
            $audio_data = array();
            
            $cteni1_url = get_post_meta($post->ID, 'audio_cteni_1', true);
            $cteni2_url = get_post_meta($post->ID, 'audio_cteni_2', true);
            $evangelium_url = get_post_meta($post->ID, 'audio_evangelium', true);

            if (!empty($cteni1_url)) $audio_data['cteni_1'] = esc_url($cteni1_url);
            if (!empty($cteni2_url)) $audio_data['cteni_2'] = esc_url($cteni2_url);
            if (!empty($evangelium_url)) $audio_data['evangelium'] = esc_url($evangelium_url);

            wp_localize_script( 'liturgicke-cteni-script', 'liturgickeUdaje', array('audioUrls' => $audio_data) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'moje_aplikace_assets' );

// END ENQUEUE PARENT ACTION
