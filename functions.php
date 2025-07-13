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
require_once( $inc_dir . '/nastaveni-administrace.php' );
require_once( $inc_dir . '/nastaveni-youtube.php' );
require_once( $inc_dir . '/nastaveni-radia.php' );
require_once( $inc_dir . '/nastaveni-navodu.php' );

// Načtení logiky pro odesílání e-mailů (pokud existuje)
if ( file_exists( get_stylesheet_directory() . '/ecomail-sender.php' ) ) {
    require_once( get_stylesheet_directory() . '/ecomail-sender.php' );
}

// Původní kód z rodičovské šablony
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
 * Načtení stylů pro stránku Nastavení vzhledu.
 */
function enqueue_nastaveni_vzhledu_assets() {
    if ( is_page_template( 'page-nastaveni-vzhledu.php' ) ) {
        wp_enqueue_style( 'page-nastaveni-vzhledu-style', get_stylesheet_directory_uri() . '/css/page-nastaveni-vzhledu.css', array(), wp_get_theme()->get('Version') );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_nastaveni_vzhledu_assets' );

/**
 * Vloží do hlavičky stránky inline CSS pro přepsání barev dolní lišty.
 */
function pehobr_output_bottom_nav_colors_css() {
    $bg_color = get_option('pehobr_bottom_nav_bg_color', '#7e7383');
    $icon_color = get_option('pehobr_bottom_nav_icon_color', '#ffffff');
    $convex_bg_color = get_option('pehobr_bottom_nav_convex_bg_color', '#ffffff');
    $active_icon_color = get_option('pehobr_bottom_nav_active_icon_color', '#7e7383');

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


// =========================================================================
// SEKCE PRO AI INSPIRACI A SOUVISEJÍCÍ SKRIPTY
// =========================================================================

/**
 * Správné načtení a lokalizace skriptů pro ÚVODNÍ STRÁNKU (HOME).
 * Tato funkce zajišťuje, že page-home.js má k dispozici data z PHP.
 */
function pehobr_enqueue_home_assets() {
    // Spustí se pouze na stránce s šablonou 'page-home.php'
    if ( is_page_template( 'page-home.php' ) ) {
        $theme_version = wp_get_theme()->get('Version');

        // 1. Načteme skript pro úvodní stránku
        wp_enqueue_script(
            'page-home-js',
            get_stylesheet_directory_uri() . '/js/page-home.js',
            array('jquery'),
            $theme_version, // Verze pro správu cache
            true // Načíst v patičce
        );

        // 2. IHNED POTÉ připojíme data pro AJAX (vytvoří objekt 'pehobr_ajax' v JS)
        wp_localize_script('page-home-js', 'pehobr_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('pehobr_home_inspiration_nonce')
        ));

        // Načtení skriptů pro Lightbox2 (přesunuto sem pro jistotu)
        wp_enqueue_style( 'lightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css', array(), '2.11.3' );
        wp_enqueue_script( 'lightbox-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', array('jquery'), '2.11.3', true );
    }
}
add_action( 'wp_enqueue_scripts', 'pehobr_enqueue_home_assets', 20 ); // Priorita 20, aby se spustila po ostatních


/**
 * AJAX endpoint pro generování inspirace v modálním okně na úvodní stránce.
 */
add_action('wp_ajax_pehobr_generate_home_inspiration', 'pehobr_generate_home_inspiration_callback');
add_action('wp_ajax_nopriv_pehobr_generate_home_inspiration', 'pehobr_generate_home_inspiration_callback');

function pehobr_generate_home_inspiration_callback() {
    check_ajax_referer('pehobr_home_inspiration_nonce', 'nonce');

    $api_key = defined('GOOGLE_AI_API_KEY') ? GOOGLE_AI_API_KEY : '';
    if (empty($api_key)) {
        wp_send_json_error(['content' => 'Chyba: API klíč pro AI není nastaven v souboru wp-config.php.']);
        return;
    }

    $daily_title = isset($_POST['daily_title']) ? sanitize_text_field($_POST['daily_title']) : 'dnešní den';
    $daily_date = isset($_POST['daily_date']) ? sanitize_text_field($_POST['daily_date']) : '';
    $user_profile = isset($_POST['user_profile']) ? sanitize_textarea_field($_POST['user_profile']) : 'Uživatel hledá povzbuzení.';

    $prompt = "Jsi moudrý a laskavý kněz. Poskytni krátkou duchovní inspiraci (cca 4-6 vět) pro křesťana. Tvá odpověď musí být povzbudivá a srozumitelná. Mluv přímo k uživateli. Nezačínej oslovením, ale rovnou myšlenkou. Neformátuj text (žádné nadpisy, tučné písmo ani odrážky). Odpověz v českém jazyce. Inspiruj se těmito informacemi: Liturgický den: '{$daily_title} ({$daily_date})'. Situace uživatele: '{$user_profile}'.";

    $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $api_key;
    $request_body = ['contents' => [['parts' => [['text' => $prompt]]]]];

    $response = wp_remote_post($api_url, [
        'method'  => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode($request_body),
        'timeout' => 60,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['content' => 'Chyba při komunikaci se serverem. Zkontrolujte prosím své připojení k internetu.']);
        return;
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($response_body['candidates'][0]['content']['parts'][0]['text'])) {
        $generated_text = $response_body['candidates'][0]['content']['parts'][0]['text'];
        wp_send_json_success(['content' => $generated_text]);
    } else {
        wp_send_json_error(['content' => 'Omlouváme se, AI se nepodařilo vygenerovat text. Zkuste to prosím znovu.', 'details' => $response_body]);
    }
}


/**
 * PŮVODNÍ FUNKCE: AJAX endpoint pro stránku /inspirace-ai/
 */
add_action('wp_ajax_generate_ai_inspiration', 'handle_ai_inspiration_request');
add_action('wp_ajax_nopriv_generate_ai_inspiration', 'handle_ai_inspiration_request');

function handle_ai_inspiration_request() {
    if (!isset($_POST['situations']) || !isset($_POST['scripture'])) {
        wp_send_json_error(['message' => 'Chybějící data.'], 400);
        return;
    }

    $situations_text = sanitize_textarea_field($_POST['situations']);
    $scripture_text = sanitize_textarea_field($_POST['scripture']);
    
    $api_key = defined('GOOGLE_AI_API_KEY') ? GOOGLE_AI_API_KEY : '';
    if (empty($api_key)) {
        wp_send_json_error(['message' => 'Chyba serveru: API klíč není nastaven.'], 500);
        return;
    }
    
    $prompt = "TÓN A STYL ODPOVĚDI:\n";
    $prompt .= "- **Přívětivý a povzbudivý:** Text má působit jako tiché duchovní povzbuzení, jako myšlenka, která zahřeje a nese naději.\n";
    $prompt .= "- **Jednoduchý a srozumitelný:** Vyhnout se složité teologii, psát jazykem blízkým běžnému člověku.\n";
    $prompt .= "- **Klidný a citlivý:** Měl by být jako duchovní zastavení - laskavé, tiché, hluboké.\n";
    $prompt .= "- **Všeobecné duchovní zamyšlení:** Není to přímé oslovování konkrétní osoby, ale krátká inspirace pro každého, kdo prochází těžším obdobím.\n";
    $prompt .= "- **Nezmiňuj konkrétní životní situace:** Nepopisuj nebo neopakuj situaci uživatele, pouze se jí nech inspirovat v tónu nebo podtextu.\n\n";
    $prompt .= "ŽIVOTNÍ SITUACE UŽIVATELE (POUZE PRO INSPIRACI, NEZMIŇUJ PŘÍMO):\n" . $situations_text . "\n\n";
    $prompt .= "DNEŠNÍ BOŽÍ SLOVO (EVANGELIUM A ČTENÍ):\n" . $scripture_text . "\n\n";
    $prompt .= "ÚKOL:\n";
    $prompt .= "1. Najdi hlavní myšlenku nebo téma v dnešním Božím slově.\n";
    $prompt .= "2. Napiš krátké (cca 5-7 vět) duchovní zamyšlení inspirované touto myšlenkou.\n";
    $prompt .= "3. Připoj krátkou modlitbu nebo povzbudivé zvolání (v jednotném čísle) k Bohu Otci, pak k Ježíši Kristu a k Duchu svatému, vždy v jedné větě.\n";
    $prompt .= "4. A na závěr krátkou modlitbu k Panně Marii.\n";
    $prompt .= "Odpověz v českém jazyce.";

    $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $api_key;
    $request_body = ['contents' => [['parts' => [['text' => $prompt]]]]];

    $response = wp_remote_post($api_url, [
        'method'  => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode($request_body),
        'timeout' => 60,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Chyba při komunikaci s AI službou.'], 500);
        return;
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($response_body['candidates'][0]['content']['parts'][0]['text'])) {
        $generated_text = $response_body['candidates'][0]['content']['parts'][0]['text'];
        wp_send_json_success(['inspiration' => $generated_text]);
    } else {
        wp_send_json_error(['message' => 'AI se nepodařilo vygenerovat text.', 'details' => $response_body], 500);
    }
}
?>