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

/**
 * Registrace vlastního typu obsahu (Custom Post Type) pro denní citáty.
 */
function pehobr_register_daily_drops_cpt() {
    $labels = array(
        'name'                  => _x( 'Denní kapky', 'Post Type General Name', 'minimalistblogger-child' ),
        'singular_name'         => _x( 'Denní kapka', 'Post Type Singular Name', 'minimalistblogger-child' ),
        'menu_name'             => __( 'Denní kapky', 'minimalistblogger-child' ),
        'name_admin_bar'        => __( 'Denní kapka', 'minimalistblogger-child' ),
        'archives'              => __( 'Archiv Denních kapek', 'minimalistblogger-child' ),
        'attributes'            => __( 'Atributy Denní kapky', 'minimalistblogger-child' ),
        'parent_item_colon'     => __( 'Rodičovská položka:', 'minimalistblogger-child' ),
        'all_items'             => __( 'Všechny Denní kapky', 'minimalistblogger-child' ),
        'add_new_item'          => __( 'Přidat novou Denní kapku', 'minimalistblogger-child' ),
        'add_new'               => __( 'Přidat novou', 'minimalistblogger-child' ),
        'new_item'              => __( 'Nová Denní kapka', 'minimalistblogger-child' ),
        'edit_item'             => __( 'Upravit Denní kapku', 'minimalistblogger-child' ),
        'update_item'           => __( 'Aktualizovat Denní kapku', 'minimalistblogger-child' ),
        'view_item'             => __( 'Zobrazit Denní kapku', 'minimalistblogger-child' ),
        'view_items'            => __( 'Zobrazit Denní kapky', 'minimalistblogger-child' ),
        'search_items'          => __( 'Hledat Denní kapku', 'minimalistblogger-child' ),
    );
    $args = array(
        'label'                 => __( 'Denní kapka', 'minimalistblogger-child' ),
        'description'           => __( 'Obsah pro denní zobrazení na úvodní stránce.', 'minimalistblogger-child' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'custom-fields' ), // Podporuje název a vlastní pole
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-calendar-alt', // Ikonka v menu
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type( 'denni_kapka', $args );
}
add_action( 'init', 'pehobr_register_daily_drops_cpt', 0 );

/**
 * Vytvoření vlastního REST API endpointu pro import Denních kapek.
 * Endpoint bude dostupný na adrese: /wp-json/pkapky/v1/import
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'pkapky/v1', '/import', array(
        'methods' => 'POST',
        'callback' => 'pehobr_handle_daily_drops_import',
        'permission_callback' => 'pehobr_import_permission_check'
    ) );
} );

/**
 * Bezpečnostní kontrola pro import.
 * Povolí přístup pouze pokud požadavek obsahuje správný tajný klíč.
 */
function pehobr_import_permission_check( $request ) {
    // --- ZDE NASTAVTE SVŮJ TAJNÝ KLÍČ ---
    // Můžete si vymyslet jakýkoliv dlouhý a složitý řetězec.
    $secret_key = 'TohleJedfs52985MujSuperTa665dfs2jnyKlicProMadfsdf6s8e2keDotCom';
    // ------------------------------------
    
    $sent_key = $request->get_header('X-Import-Key'); // Klíč očekáváme v hlavičce požadavku

    if ( $sent_key === $secret_key ) {
        return true; // Klíče se shodují, přístup povolen
    }
    
    // Pro debugování můžete odkomentovat následující řádek:
    // error_log('Pokus o neautorizovaný import. Odeslaný klíč: ' . $sent_key);

    return new WP_Error( 'rest_forbidden', 'Neplatný bezpečnostní klíč.', array( 'status' => 403 ) );
}

/**
 * Funkce, která zpracuje data odeslaná z Make.com.
 */
function pehobr_handle_daily_drops_import( WP_REST_Request $request ) {
    $items = $request->get_json_params();
    $created_posts = 0;
    $updated_posts = 0;

    if ( empty( $items ) ) {
        return new WP_Error( 'no_data', 'Nebyla přijata žádná data k importu.', array( 'status' => 400 ) );
    }

    // Projdeme všechny položky (řádky z tabulky) odeslané z Make.com
    foreach ( $items as $item ) {
        // Základní kontrola, zda máme potřebná data
        if ( !isset( $item['datum'] ) || !isset( $item['nazev'] ) ) {
            continue; // Přeskočíme položku, pokud nemá datum nebo název
        }

        // Připravíme data pro vytvoření nebo aktualizaci příspěvku
        $post_args = array(
            'post_type'    => 'denni_kapka',
            'post_title'   => sanitize_text_field( $item['nazev'] ),
            'post_content' => '', // Obsah nepotřebujeme
            'post_status'  => 'publish',
            'post_date'    => sanitize_text_field( $item['datum'] ) . ' 12:00:00', // Publikujeme v poledne daného dne
        );

        // Zkusíme najít, zda příspěvek s tímto datem již neexistuje
        $existing_posts = get_posts(array(
            'post_type' => 'denni_kapka',
            'date_query' => array(
                'year'  => substr($item['datum'], 0, 4),
                'month' => substr($item['datum'], 5, 2),
                'day'   => substr($item['datum'], 8, 2),
            ),
        ));

        if ( $existing_posts ) {
            // Příspěvek existuje, aktualizujeme ho
            $post_id = $existing_posts[0]->ID;
            $post_args['ID'] = $post_id;
            wp_update_post( $post_args );
            $updated_posts++;
        } else {
            // Příspěvek neexistuje, vytvoříme nový
            $post_id = wp_insert_post( $post_args );
            $created_posts++;
        }

        // Pokud byl příspěvek úspěšně vytvořen/aktualizován, uložíme vlastní pole (citáty)
        if ( $post_id && !is_wp_error( $post_id ) ) {
            // Projdeme všechny možné klíče citátů
            $citat_keys = ['citat_janpavel', 'citat_benedikt', 'citat_frantisek', 'citat_modlitba', 'citat_lev', 'citat_citaty', 'citat_svatost', 'citat_texty', 'citat_komunita'];
            foreach ($citat_keys as $key) {
                if ( isset($item[$key]) ) {
                    // Povolíme HTML kód, který chcete používat
                    $allowed_html = [ 'p' => ['style' => []], 'em' => [], 'strong' => [], 'br' => [], 'span' => ['style' => []] ];
                    $sanitized_value = wp_kses($item[$key], $allowed_html);
                    update_post_meta( $post_id, $key, $sanitized_value );
                }
            }
        }
    }

    // Vrátíme odpověď pro Make.com
    return new WP_REST_Response(
        array(
            'status' => 'success',
            'message' => 'Import dokončen.',
            'created' => $created_posts,
            'updated' => $updated_posts
        ), 200 );
}