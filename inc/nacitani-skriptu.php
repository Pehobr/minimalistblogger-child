<?php
/**
 * Načítání všech CSS stylů a JavaScriptových skriptů.
 * VERZE s přidáním skriptů a stylů pro vyskakovací okno.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Načtení všech vlastních stylů a skriptů pro child šablonu.
 */
function minimalistblogger_child_enqueue_assets() {
    $theme_version = wp_get_theme()->get('Version');

    // --- Načtení CSS stylů ---
    wp_enqueue_style( 'minimalistblogger-dolni-lista', get_stylesheet_directory_uri() . '/css/dolni-lista.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-fixni-hlavicka', get_stylesheet_directory_uri() . '/css/fixni-hlavicka.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-prispevky', get_stylesheet_directory_uri() . '/css/prispevky.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-vzhled-mobil', get_stylesheet_directory_uri() . '/css/vzhled-mobil.css', array('chld_thm_cfg_parent'), $theme_version );
    wp_enqueue_style( 'minimalistblogger-vzhled-pc', get_stylesheet_directory_uri() . '/css/vzhled-pc.css', array('chld_thm_cfg_parent'), $theme_version, 'screen and (min-width: 992px)' );

    if (file_exists(get_stylesheet_directory() . '/css/mobile-menu.css')) {
        wp_enqueue_style( 'minimalistblogger-mobile-menu', get_stylesheet_directory_uri() . '/css/mobile-menu.css', array(), filemtime( get_stylesheet_directory() . '/css/mobile-menu.css' ) );
    }
    if (file_exists(get_stylesheet_directory() . '/js/mobile-menu.js')) {
        wp_enqueue_script( 'minimalistblogger-mobile-menu-js', get_stylesheet_directory_uri() . '/js/mobile-menu.js', array('jquery'), filemtime( get_stylesheet_directory() . '/js/mobile-menu.js' ), true );
    }

    if ( is_page_template('page-liturgicke-cteni.php') || is_page_template('page-poboznosti.php') || is_page_template('page-home.php') || is_page_template('page-radio.php') ) {
        wp_enqueue_style( 'minimalistblogger-nastaveni-panel', get_stylesheet_directory_uri() . '/css/nastaveni-panel.css', array('chld_thm_cfg_parent'), $theme_version );
        wp_enqueue_script( 'minimalistblogger-nastaveni-panel-js', get_stylesheet_directory_uri() . '/js/nastaveni-panel.js', array('jquery'), $theme_version, true );
    }

    if ( is_page_template('page-home.php') ) {
        wp_enqueue_style( 'postni-kapky-home-styles', get_stylesheet_directory_uri() . '/css/page-home.css', array(), filemtime( get_stylesheet_directory() . '/css/page-home.css' ) );
        
        // Načtení stylů pro vyskakovací okno s prosbou o dar
        if (file_exists(get_stylesheet_directory() . '/css/donation-popup.css')) {
            wp_enqueue_style( 'donation-popup-style', get_stylesheet_directory_uri() . '/css/donation-popup.css', array(), filemtime( get_stylesheet_directory() . '/css/donation-popup.css' ) );
        }

        if (file_exists(get_stylesheet_directory() . '/js/page-home.js')) {
            wp_enqueue_script( 'postni-kapky-home-js', get_stylesheet_directory_uri() . '/js/page-home.js', array('jquery'), filemtime( get_stylesheet_directory() . '/js/page-home.js' ), true );
        }

        // Předání PHP nastavení do JavaScriptu pro vyskakovací okno
        wp_localize_script(
            'postni-kapky-home-js',
            'donation_popup_settings',
            array(
                'show_popup' => get_option('pehobr_show_donation_popup') === 'on'
            )
        );
    }

    if ( is_page_template('page-oblibene.php') ) {
        wp_enqueue_style( 'minimalistblogger-oblibene-style', get_stylesheet_directory_uri() . '/css/oblibene.css', array('chld_thm_cfg_parent'), filemtime( get_stylesheet_directory() . '/css/oblibene.css' ) );
        wp_enqueue_script( 'minimalistblogger-oblibene-js', get_stylesheet_directory_uri() . '/js/oblibene.js', array('jquery'), filemtime( get_stylesheet_directory() . '/js/oblibene.js' ), true );
    }

    if ( is_page_template('page-navody.php') ) {
        wp_enqueue_style( 'minimalistblogger-navody-style', get_stylesheet_directory_uri() . '/css/navody.css', array('chld_thm_cfg_parent'), filemtime( get_stylesheet_directory() . '/css/navody.css' ) );
    }

    wp_enqueue_script( 'minimalistblogger-dolni-lista-js', get_stylesheet_directory_uri() . '/js/dolni-lista.js', array(), $theme_version, true );

    if ( is_page_template('page-radio.php') ) {
        wp_enqueue_style( 'minimalistblogger-page-radia', get_stylesheet_directory_uri() . '/css/page-radia.css', array('chld_thm_cfg_parent'), filemtime( get_stylesheet_directory() . '/css/page-radia.css' ) );
        wp_enqueue_script( 'minimalistblogger-page-radia-js', get_stylesheet_directory_uri() . '/js/page-radia.js', array('jquery'), filemtime( get_stylesheet_directory() . '/js/page-radia.js' ), true );
        wp_localize_script( 'minimalistblogger-page-radia-js', 'radio_page_settings', array( 'template_url' => get_stylesheet_directory_uri() ) );
    }

    if (file_exists(get_stylesheet_directory() . '/css/left-mobile-menu.css')) {
        wp_enqueue_style( 'minimalistblogger-left-mobile-menu-css', get_stylesheet_directory_uri() . '/css/left-mobile-menu.css', array(), filemtime( get_stylesheet_directory() . '/css/left-mobile-menu.css' ) );
    }
    if (file_exists(get_stylesheet_directory() . '/js/left-mobile-menu.js')) {
        wp_enqueue_script( 'minimalistblogger-left-mobile-menu-js', get_stylesheet_directory_uri() . '/js/left-mobile-menu.js', array('jquery'), filemtime( get_stylesheet_directory() . '/js/left-mobile-menu.js' ), true );
    }

    if ( is_page_template('page-videokapky.php') ) {
        wp_enqueue_style( 'page-videokapky-style', get_stylesheet_directory_uri() . '/css/page-videokapky.css', array(), '1.1' );
        wp_enqueue_script( 'page-videokapky-js', get_stylesheet_directory_uri() . '/js/page-videokapky.js', array('jquery'), '1.0', true );
        if (file_exists(get_stylesheet_directory() . '/css/page-audio-youtube.css')) {
            wp_enqueue_style( 'page-audio-youtube-style', get_stylesheet_directory_uri() . '/css/page-audio-youtube.css', array(), filemtime( get_stylesheet_directory() . '/css/page-audio-youtube.css' ) );
        }
        if (file_exists(get_stylesheet_directory() . '/js/page-audio-youtube.js')) {
            wp_enqueue_script( 'page-audio-youtube-js', get_stylesheet_directory_uri() . '/js/page-audio-youtube.js', array('jquery'), filemtime( get_stylesheet_directory() . '/js/page-audio-youtube.js' ), true );
        }
    }

    if ( is_page_template('page-papezlev.php') ) {
        wp_enqueue_style( 'minimalistblogger-papezlev-style', get_stylesheet_directory_uri() . '/css/page-papezlev.css', array('chld_thm_cfg_parent'), $theme_version );
        wp_enqueue_script( 'minimalistblogger-papezlev-js', get_stylesheet_directory_uri() . '/js/page-papezlev.js', array('jquery'), $theme_version, true );
    }

    if ( is_page_template('page-postni-pisne.php') ) {
        wp_enqueue_style( 'page-postni-pisne-style', get_stylesheet_directory_uri() . '/css/page-postni-pisne.css', array(), '1.0' );
        wp_enqueue_script( 'page-postni-pisne-js', get_stylesheet_directory_uri() . '/js/page-postni-pisne.js', array('jquery'), '1.0', true );
    }

    if ( is_page_template('page-playlist-audio.php') ) {
        wp_enqueue_style( 'page-playlist-audio-style', get_stylesheet_directory_uri() . '/css/page-playlist-audio.css', array(), '1.0' );
        wp_enqueue_script( 'page-playlist-audio-js', get_stylesheet_directory_uri() . '/js/page-playlist-audio.js', array('jquery'), '1.0', true );
    }

    if ( is_page_template('page-audio-youtube.php') ) {
        if (file_exists(get_stylesheet_directory() . '/css/page-audio-youtube.css')) {
            wp_enqueue_style( 'page-audio-youtube-style', get_stylesheet_directory_uri() . '/css/page-audio-youtube.css', array(), filemtime( get_stylesheet_directory() . '/css/page-audio-youtube.css' ) );
        }
        if (file_exists(get_stylesheet_directory() . '/js/page-audio-youtube.js')) {
            wp_enqueue_script( 'page-audio-youtube-js', get_stylesheet_directory_uri() . '/js/page-audio-youtube.js', array('jquery'), filemtime( get_stylesheet_directory() . '/js/page-audio-youtube.js' ), true );
        }
    }

    wp_enqueue_script( 'sidebar-menu-js', get_stylesheet_directory_uri() . '/js/sidebar-menu.js', array('jquery'), wp_get_theme()->get('Version'), true );
}
add_action( 'wp_enqueue_scripts', 'minimalistblogger_child_enqueue_assets', 20 );

function pehobr_enqueue_admin_scripts($hook) {
    if ( 'postni-kapky_page_pehobr-radio-settings' !== $hook && 'postni-kapky_page_nastaveni-poradi-navodu' !== $hook) {
        return;
    }
    wp_enqueue_script('jquery-ui-sortable');
}
add_action( 'admin_enqueue_scripts', 'pehobr_enqueue_admin_scripts' );

function moje_aplikace_assets() {
    $theme_version = wp_get_theme()->get('Version');
    $is_liturgicka_stranka = is_page_template('page-liturgicke-cteni.php') || (is_singular('post') && has_category('liturgicke-cteni'));

    if ( $is_liturgicka_stranka ) {
        wp_enqueue_style( 'liturgicke-cteni-style', get_stylesheet_directory_uri() . '/css/liturgicke-cteni.css', array(), $theme_version );
        wp_enqueue_script( 'liturgicke-cteni-script', get_stylesheet_directory_uri() . '/js/liturgicke-cteni.js', array('jquery'), $theme_version, true );

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

function poboznosti_app_assets() {
    $is_poboznosti_stranka = is_page_template('page-poboznosti.php') || (is_singular('post') && has_category('poboznosti'));

    if ( $is_poboznosti_stranka ) {
        $theme_version = wp_get_theme()->get('Version');
        wp_enqueue_style( 'poboznosti-style', get_stylesheet_directory_uri() . '/css/poboznosti.css', array(), $theme_version );
        wp_enqueue_script( 'poboznosti-script', get_stylesheet_directory_uri() . '/js/poboznosti.js', array('jquery'), $theme_version, true );
    }
}
add_action( 'wp_enqueue_scripts', 'poboznosti_app_assets' );

function enqueue_archiv_citatu_assets() {
    if ( is_page_template( 'page-archiv-citatu.php' ) ) {
        $theme_version = wp_get_theme()->get('Version');
        
        wp_enqueue_style( 'archiv-citatu-style', get_stylesheet_directory_uri() . '/css/archiv-citatu.css', array(), $theme_version );
        wp_enqueue_script( 'isotope', 'https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js', array('jquery'), '3.0.6', true );
        wp_enqueue_script( 'archiv-citatu-script', get_stylesheet_directory_uri() . '/js/archiv-citatu.js', array( 'jquery', 'isotope' ), $theme_version, true );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_archiv_citatu_assets', 30 );

function enqueue_zapisnik_assets() {
    if ( is_page_template( 'page-zapisnik.php' ) ) {
        $theme_version = wp_get_theme()->get('Version');
        wp_enqueue_style( 'zapisnik-style', get_stylesheet_directory_uri() . '/css/zapisnik.css', array('chld_thm_cfg_parent'), $theme_version );
        wp_enqueue_script( 'zapisnik-script', get_stylesheet_directory_uri() . '/js/zapisnik.js', array( 'jquery' ), $theme_version, true );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_zapisnik_assets' );

function enqueue_podcast_assets() {
    if ( is_page_template( 'page-podcast.php' ) ) {
        wp_enqueue_style( 'page-podcast-style', get_stylesheet_directory_uri() . '/css/page-podcast.css', array(), wp_get_theme()->get('Version') );
        wp_enqueue_script( 'page-podcast-script', get_stylesheet_directory_uri() . '/js/page-podcast.js', array( 'jquery' ), wp_get_theme()->get('Version'), true );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_podcast_assets' );

function enqueue_prezentace_assets() {
    if ( is_page_template( 'page-prezentace.php' ) ) {
        wp_enqueue_style( 'prezentace-style', get_stylesheet_directory_uri() . '/css/prezentace.css', array('chld_thm_cfg_parent'), wp_get_theme()->get('Version') );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_prezentace_assets' );