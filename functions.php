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

/**
 * AJAX endpoint pro generování inspirace pomocí AI.
 */
// Akce pro přihlášené i nepřihlášené uživatele
add_action('wp_ajax_generate_ai_inspiration', 'handle_ai_inspiration_request');
add_action('wp_ajax_nopriv_generate_ai_inspiration', 'handle_ai_inspiration_request');

function handle_ai_inspiration_request() {
    // Zkontroluje, zda byly odeslány potřebné údaje
    if (!isset($_POST['situations']) || !isset($_POST['scripture'])) {
        wp_send_json_error(['message' => 'Chybějící data.'], 400);
        return;
    }

    // Načte data odeslaná z JavaScriptu
    $situations_text = sanitize_textarea_field($_POST['situations']);
    $scripture_text = sanitize_textarea_field($_POST['scripture']);
    
    // Načte API klíč z wp-config.php
    $api_key = defined('GOOGLE_AI_API_KEY') ? GOOGLE_AI_API_KEY : '';
    if (empty($api_key)) {
        wp_send_json_error(['message' => 'Chyba serveru: API klíč není nastaven.'], 500);
        return;
    }

    // =================================================================
    // NOVÁ ČÁST: DEFINICE TÓNU A STYLU ODPOVĚDI
    // =================================================================
    // Zde přesně specifikujeme, jaký styl má AI použít.
    // Používáme vaše klíčová slova.
   $prompt .= "TÓN A STYL ODPOVĚDI:\n";

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
    // =================================================================

    // API URL pro model Gemini
    $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $api_key;

    // Tělo požadavku pro Google AI API
    $request_body = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    // Odeslání požadavku na Google AI API pomocí WordPress funkce
    $response = wp_remote_post($api_url, [
        'method'    => 'POST',
        'headers'   => ['Content-Type' => 'application/json'],
        'body'      => json_encode($request_body),
        'timeout'   => 60, // Počkat až 60 sekund
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Chyba při komunikaci s AI službou.'], 500);
        return;
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // Zpracování odpovědi od AI
    if (isset($response_body['candidates'][0]['content']['parts'][0]['text'])) {
        $generated_text = $response_body['candidates'][0]['content']['parts'][0]['text'];
        wp_send_json_success(['inspiration' => $generated_text]);
    } else {
        // Pošle chybu, pokud AI vrátila neočekávanou odpověď
        wp_send_json_error(['message' => 'AI se nepodařilo vygenerovat text.', 'details' => $response_body], 500);
    }
}