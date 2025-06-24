<?php
// Registrace nové pozice pro mobilní menu
function register_mobile_menu_location() {
    register_nav_menus(
        array(
            'mobile_extra_menu' => __( 'Extra mobilní menu', 'minimalistblogger-child' ),
        )
    );
}
add_action( 'init', 'register_mobile_menu_location' );

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

    // Načtení stylů pro panel nastavení - načítáme pouze na stránkách s aplikacemi
    if ( is_page_template('page-liturgicke-cteni.php') || is_page_template('page-poboznosti.php') || is_page_template('page-home.php') ) {
        wp_enqueue_style(
            'minimalistblogger-nastaveni-panel',
            get_stylesheet_directory_uri() . '/css/nastaveni-panel.css',
            array('chld_thm_cfg_parent'),
            $theme_version
        );
        // Načtení JavaScriptu pro panel nastavení - pro obě aplikace
        wp_enqueue_script(
            'minimalistblogger-nastaveni-panel-js',
            get_stylesheet_directory_uri() . '/js/nastaveni-panel.js',
            array('jquery'),
            $theme_version,
            true
        );
    }

    // --- Načtení CSS a JS pro úvodní stránku aplikace (HOME) ---
    if ( is_page_template('page-home.php') ) { // Načíst pouze na úvodní stránce
        wp_enqueue_style(
            'postni-kapky-home-styles', // Unikátní název pro váš styl
            get_stylesheet_directory_uri() . '/css/page-home.css', // Cesta k vašemu CSS souboru
            array(), // Závislosti (žádné prozatím)
            filemtime( get_stylesheet_directory() . '/css/page-home.css' ) // Verze souboru pro zamezení cache
        );
        
        // Zde byl dříve kód pro page-home.js, který obsluhoval modální okno s citáty.
        // Nyní ho přesuneme, aby byl spolu s menu.

        // Načtení JS pro modální okno s citáty (pokud ho stále chcete používat)
        if (file_exists(get_stylesheet_directory() . '/js/page-home.js')) {
            wp_enqueue_script(
                'postni-kapky-home-js', // Unikátní název pro skript
                get_stylesheet_directory_uri() . '/js/page-home.js', // Cesta k JS souboru pro modální okna
                array('jquery'), // Závislost na jQuery
                filemtime( get_stylesheet_directory() . '/js/page-home.js' ), // Verze
                true // Načíst v patičce
            );
        }

        // Načtení stylů a skriptů pro nové mobilní menu
        if (file_exists(get_stylesheet_directory() . '/css/mobile-menu.css')) {
            wp_enqueue_style(
                'minimalistblogger-mobile-menu',
                get_stylesheet_directory_uri() . '/css/mobile-menu.css',
                array(),
                filemtime( get_stylesheet_directory() . '/css/mobile-menu.css' )
            );
        }
        if (file_exists(get_stylesheet_directory() . '/js/mobile-menu.js')) {
            wp_enqueue_script(
                'minimalistblogger-mobile-menu-js',
                get_stylesheet_directory_uri() . '/js/mobile-menu.js',
                array('jquery'),
                filemtime( get_stylesheet_directory() . '/js/mobile-menu.js' ),
                true
            );
        }
    }

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

/**
 * Načtení specifických stylů a skriptů pro šablonu "Aplikace Pobožnosti"
 */
function poboznosti_app_assets() {
    // Podmínka: Načíst na stránce s šablonou Pobožnosti NEBO na detailu příspěvku z dané rubriky
    $is_poboznosti_stranka = is_page_template('page-poboznosti.php') || (is_singular('post') && has_category('poboznosti'));

    if ( $is_poboznosti_stranka ) {
        $theme_version = wp_get_theme()->get('Version');
        
        // Načtení CSS souboru pro pobožnosti
        wp_enqueue_style( 
            'poboznosti-style', 
            get_stylesheet_directory_uri() . '/css/poboznosti.css', 
            array(),
            $theme_version 
        );

        // Načtení JavaScriptového souboru pro pobožnosti
        wp_enqueue_script( 
            'poboznosti-script', 
            get_stylesheet_directory_uri() . '/js/poboznosti.js', 
            array('jquery'),
            $theme_version, 
            true
        );

        // Předání dat z PHP do JS (pouze pokud nějaká jsou)
        global $post;
        $audio_data = [];
        $i = 1;
        while(true) {
            $meta_key = 'audio_cast_' . $i;
            $url = get_post_meta($post->ID, $meta_key, true);
            if (!empty($url)) {
                $audio_data[$meta_key] = esc_url($url);
                $i++;
            } else {
                break;
            }
        }
        
        // Předáme data, pouze pokud nějaká jsou
        if (!empty($audio_data)) {
            wp_localize_script( 'poboznosti-script', 'poboznostiUdaje', array('audioUrls' => $audio_data) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'poboznosti_app_assets' );

// END ENQUEUE PARENT ACTION