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

    // --- Načtení mobilního menu na VŠECH stránkách ---
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

    // --- Načtení CSS a JS POUZE pro stránku s oblíbenými položkami ---
    if ( is_page_template('page-oblibene.php') ) {
        wp_enqueue_style(
            'minimalistblogger-oblibene-style',
            get_stylesheet_directory_uri() . '/css/oblibene.css',
            array('chld_thm_cfg_parent'),
            filemtime( get_stylesheet_directory() . '/css/oblibene.css' )
        );
        wp_enqueue_script(
            'minimalistblogger-oblibene-js',
            get_stylesheet_directory_uri() . '/js/oblibene.js',
            array('jquery'),
            filemtime( get_stylesheet_directory() . '/js/oblibene.js' ),
            true
        );
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
            array(), $theme_version 
        );
        wp_enqueue_script( 
            'liturgicke-cteni-script', 
            get_stylesheet_directory_uri() . '/js/liturgicke-cteni.js', 
            array('jquery'), $theme_version, true
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
            array(), $theme_version 
        );
        wp_enqueue_script( 
            'poboznosti-script', 
            get_stylesheet_directory_uri() . '/js/poboznosti.js', 
            array('jquery'), $theme_version, true 
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
 */
function pehobr_add_liturgical_color_body_class($classes) {
    $config_path = get_stylesheet_directory() . '/zmena-liturgicke-barvy.php';
    if ( file_exists($config_path) ) {
        $liturgicky_kalendar = include $config_path;
    } else {
        $liturgicky_kalendar = array();
    }

    $timezone = new DateTimeZone('Europe/Prague');
    $dnesni_datum_obj = new DateTime('now', $timezone);

    $trvala_zmena_od_data = new DateTime('2026-04-06', $timezone);
    $barva_po_zmene = 'bezova';
    $vychozi_barva = 'fialova';
    $barva_dnes = $vychozi_barva;

    if ($dnesni_datum_obj >= $trvala_zmena_od_data) {
        $barva_dnes = $barva_po_zmene;
    } else {
        $dnesni_datum_format = $dnesni_datum_obj->format('Y-m-d');
        if (isset($liturgicky_kalendar[$dnesni_datum_format])) {
            $barva_dnes = $liturgicky_kalendar[$dnesni_datum_format];
        }
    }

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
        'supports'              => array( 'title', 'custom-fields' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-calendar-alt',
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
 * ===================================================================
 * Vytvoření vlastní stránky s nastavením pro aplikaci
 * ===================================================================
 */

/**
 * Registrace nové stránky v menu "Nastavení".
 */
function pehobr_register_settings_page() {
    add_options_page(
        'Nastavení Postní kapky',      // <<< ZMĚNA ZDE: Název stránky (v titulku okna)
        'Nastavení Postní kapky',      // <<< ZMĚNA ZDE: Název v menu
        'manage_options',
        'pehobr-app-settings',
        'pehobr_render_settings_page_content'
    );
}
add_action( 'admin_menu', 'pehobr_register_settings_page' );

/**
 * Registrace samotného nastavení (aby ho WordPress uměl uložit).
 */
function pehobr_register_settings() {
    register_setting(
        'pehobr_app_options_group',
        'start_date_setting',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '2026-02-18',
        )
    );
}
add_action( 'admin_init', 'pehobr_register_settings' );

/**
 * Funkce, která vykreslí HTML obsah naší nové stránky s nastavením.
 */
function pehobr_render_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Nastavení Postní kapky</h1> <form action="options.php" method="post">
            <?php
            settings_fields( 'pehobr_app_options_group' );
            do_settings_sections( 'pehobr-app-settings' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="start_date_setting">Datum začátku doby postní (Popeleční středa):</label>
                    </th>
                    <td>
                        <input type="date" id="start_date_setting" name="start_date_setting" value="<?php echo esc_attr( get_option( 'start_date_setting', '2026-02-18' ) ); ?>" />
                        <p class="description">
                            Zadejte datum, od kterého se má začít odpočítávat denní obsah na úvodní stránce. Formát: RRRR-MM-DD.
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Uložit změny' ); ?>
        </form>
    </div>
    <?php
}