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
    wp_enqueue_style( 'minimalistblogger-dolni-lista', get_stylesheet_directory_uri() . '/css/dolni-lista.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-fixni-hlavicka', get_stylesheet_directory_uri() . '/css/fixni-hlavicka.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-prispevky', get_stylesheet_directory_uri() . '/css/prispevky.css', array('chld_thm_cfg_parent'), $theme_version );

    // --- Načtení NOVĚ ORGANIZOVANÝCH STYLŮ ---
    wp_enqueue_style( 'minimalistblogger-vzhled-mobil', get_stylesheet_directory_uri() . '/css/vzhled-mobil.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-vzhled-pc', get_stylesheet_directory_uri() . '/css/vzhled-pc.css', array('chld_thm_cfg_parent'), $theme_version, 'screen and (min-width: 992px)' );

    // --- ZMĚNA: Načtení mobilního menu na VŠECH stránkách ---
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
    
    // Načtení stylů pro panel nastavení - načítáme pouze na stránkách s aplikacemi
    if ( is_page_template('page-liturgicke-cteni.php') || is_page_template('page-poboznosti.php') || is_page_template('page-home.php') || is_page_template('page-radio.php') ) {
        wp_enqueue_style(
            'minimalistblogger-nastaveni-panel',
            get_stylesheet_directory_uri() . '/css/nastaveni-panel.css',
            array('chld_thm_cfg_parent'),
            $theme_version
        );
        wp_enqueue_script(
            'minimalistblogger-nastaveni-panel-js',
            get_stylesheet_directory_uri() . '/js/nastaveni-panel.js',
            array('jquery'),
            $theme_version,
            true
        );
    }

    // --- Načtení CSS a JS POUZE pro úvodní stránku aplikace (HOME) ---
    if ( is_page_template('page-home.php') ) {
        wp_enqueue_style(
            'postni-kapky-home-styles',
            get_stylesheet_directory_uri() . '/css/page-home.css',
            array(),
            filemtime( get_stylesheet_directory() . '/css/page-home.css' )
        );
        
        if (file_exists(get_stylesheet_directory() . '/js/page-home.js')) {
            wp_enqueue_script(
                'postni-kapky-home-js',
                get_stylesheet_directory_uri() . '/js/page-home.js',
                array('jquery'),
                filemtime( get_stylesheet_directory() . '/js/page-home.js' ),
                true
            );
        }
    }

    // --- Načtení JavaScriptu pro dolní lištu ---
    wp_enqueue_script(
        'minimalistblogger-dolni-lista-js',
        get_stylesheet_directory_uri() . '/js/dolni-lista.js',
        array(),
        $theme_version,
        true
    );

    // --- Načtení CSS a JS POUZE pro stránku s přehrávačem rádií ---
    if ( is_page_template('page-radio.php') ) {
        wp_enqueue_style(
            'minimalistblogger-page-radia',
            get_stylesheet_directory_uri() . '/css/page-radia.css',
            array('chld_thm_cfg_parent'),
            filemtime( get_stylesheet_directory() . '/css/page-radia.css' )
        );
        
        wp_enqueue_script(
            'minimalistblogger-page-radia-js',
            get_stylesheet_directory_uri() . '/js/page-radia.js',
            array('jquery'),
            filemtime( get_stylesheet_directory() . '/js/page-radia.js' ),
            true
        );

        // Předání cesty k adresáři šablony do JS souboru
        wp_localize_script(
            'minimalistblogger-page-radia-js',
            'radio_page_settings',
            array(
                'template_url' => get_stylesheet_directory_uri()
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'minimalistblogger_child_enqueue_assets', 20 );

/**
 * Načtení specifických stylů a skriptů pro šablonu "Aplikace Liturgické Čtení"
 */
function moje_aplikace_assets() {
    $theme_version = wp_get_theme()->get('Version');

    $is_liturgicka_stranka = is_page_template('page-liturgicke-cteni.php') || (is_singular('post') && has_category('liturgicke-cteni'));

    if ( $is_liturgicka_stranka ) {
        
        wp_enqueue_style( 
            'liturgicke-cteni-style', 
            get_stylesheet_directory_uri() . '/css/liturgicke-cteni.css', 
            array(),
            $theme_version 
        );

        wp_enqueue_script( 
            'liturgicke-cteni-script', 
            get_stylesheet_directory_uri() . '/js/liturgicke-cteni.js', 
            array('jquery'),
            $theme_version, 
            true
        );

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
    $is_poboznosti_stranka = is_page_template('page-poboznosti.php') || (is_singular('post') && has_category('poboznosti'));

    if ( $is_poboznosti_stranka ) {
        $theme_version = wp_get_theme()->get('Version');
        
        wp_enqueue_style( 
            'poboznosti-style', 
            get_stylesheet_directory_uri() . '/css/poboznosti.css', 
            array(),
            $theme_version 
        );

        wp_enqueue_script( 
            'poboznosti-script', 
            get_stylesheet_directory_uri() . '/js/poboznosti.js', 
            array('jquery'),
            $theme_version, 
            true
        );

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
        
        if (!empty($audio_data)) {
            wp_localize_script( 'poboznosti-script', 'poboznostiUdaje', array('audioUrls' => $audio_data) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'poboznosti_app_assets' );
    
// END ENQUEUE PARENT ACTION


/**
 * Automaticky mění barevné téma webu podle liturgického kalendáře.
 * Přidává na <body> tag CSS třídu, např. 'theme-cervena'.
 */
function pehobr_add_liturgical_color_body_class($classes) {
    
    // Načteme konfiguraci barev z externího souboru pro lepší přehlednost
    $config_path = get_stylesheet_directory() . '/zmena-liturgicke-barvy.php';
    
    if ( file_exists($config_path) ) {
        $liturgicky_kalendar = include $config_path;
    } else {
        $liturgicky_kalendar = array();
    }

    // <<<=== ZDE ZAČÍNÁ NOVÁ UPRAVENÁ LOGIKA ===>>>

    // Nastavení časové zóny a získání dnešního data jako PHP objektu
    $timezone = new DateTimeZone('Europe/Prague');
    $dnesni_datum_obj = new DateTime('now', $timezone);

    // --- Definice pravidel pro změnu barvy ---
    $trvala_zmena_od_data = new DateTime('2026-04-06', $timezone);
    $barva_po_zmene = 'bezova';
    $vychozi_barva = 'fialova';

    // Ve výchozím stavu nastavíme základní barvu
    $barva_dnes = $vychozi_barva;

    // 1. Zkontrolujeme, zda jsme již v období trvalé změny
    if ($dnesni_datum_obj >= $trvala_zmena_od_data) {
        $barva_dnes = $barva_po_zmene;
    } else {
        // 2. Pokud ne, zkontrolujeme specifické svátky a slavnosti z konfiguračního souboru
        $dnesni_datum_format = $dnesni_datum_obj->format('Y-m-d');
        if (isset($liturgicky_kalendar[$dnesni_datum_format])) {
            $barva_dnes = $liturgicky_kalendar[$dnesni_datum_format];
        }
        // 3. Pokud ani tam není shoda, zůstává výchozí fialová, kterou jsme nastavili na začátku.
    }

    // Přidáme výslednou CSS třídu k ostatním třídám na <body>
    $classes[] = 'theme-' . $barva_dnes;
    
    return $classes;
}
add_filter('body_class', 'pehobr_add_liturgical_color_body_class');