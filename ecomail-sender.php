<?php
/**
 * Logika pro odesílání e-mailů přes Ecomail.
 *
 * @package minimalistblogger-child
 */

// Zabráníme přímému přístupu k souboru
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Naplánuje denní úlohu pro odeslání e-mailu, pokud ještě není naplánovaná.
 */
add_action('wp', 'pehobr_schedule_daily_email');
function pehobr_schedule_daily_email() {
    if (!wp_next_scheduled('pehobr_send_daily_email_hook')) {
        wp_schedule_event(strtotime('tomorrow 8:00am'), 'daily', 'pehobr_send_daily_email_hook');
    }
}

/**
 * Připojí naši hlavní odesílací funkci k naplánované úloze.
 */
add_action('pehobr_send_daily_email_hook', 'pehobr_find_and_send_daily_content');

/**
 * Hlavní funkce, která najde správnou "Denní kapku" a zahájí proces odeslání.
 */
function pehobr_find_and_send_daily_content() {
    $start_date_str = get_option('start_date_setting', '2026-02-18');
    try {
        $start_date = new DateTime($start_date_str, new DateTimeZone('Europe/Prague'));
        $today = new DateTime('today', new DateTimeZone('Europe/Prague'));

        if ($today < $start_date) {
            return;
        }

        $interval = $start_date->diff($today);
        $day_offset = $interval->days;

        $args = array(
            'post_type'      => 'denni_kapka',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'offset'         => $day_offset,
            'orderby'        => 'date',
            'order'          => 'ASC',
        );
        $daily_query = new WP_Query($args);

        if ($daily_query->have_posts()) {
            while ($daily_query->have_posts()) {
                $daily_query->the_post();
                $daily_post_id = get_the_ID();

                $data_for_email = [
                    'jpii_quote'      => get_post_meta($daily_post_id, 'citat_janpavel', true),
                    'benedict_quote'  => get_post_meta($daily_post_id, 'citat_benedikt', true),
                    'francis_quote'   => get_post_meta($daily_post_id, 'citat_frantisek', true),
                    'leo_quote'       => get_post_meta($daily_post_id, 'citat_lev', true),
                    'augustine_quote' => get_post_meta($daily_post_id, 'citat_augustin', true),
                    'prayer'          => get_post_meta($daily_post_id, 'citat_modlitba', true),
                    'image_url'       => get_post_meta($daily_post_id, 'image_url', true),
                    'subject'         => get_the_title(),
                    'web_view_url'    => home_url('/nahled-emailu/?kapka_id=' . $daily_post_id)
                ];

                $log_message = pehobr_trigger_ecomail_api_send($data_for_email);
                error_log($log_message); // Zápis do standardního logu
            }
        }
        wp_reset_postdata();
    } catch (Exception $e) {
        error_log('Chyba při přípravě denní kapky pro Ecomail: ' . $e->getMessage());
    }
}

/**
 * Finální funkce, která sestaví HTML a odešle data na Ecomail API ve dvou krocích.
 * @param array $data Pole s daty pro e-mail.
 * @return string Logovací zpráva o výsledku.
 */
function pehobr_trigger_ecomail_api_send($data) {
    $template_path = get_stylesheet_directory() . '/email-template.php';
    if (!file_exists($template_path)) {
        return 'Ecomail chyba: Šablona e-mailu nebyla nalezena.';
    }

    ob_start();
    extract($data);
    include($template_path);
    $html_content = ob_get_clean();

    $api_key = 'aa5b20ef43134327157d059786b0e402b540c89792374ef039ff025f2e525923';
    $list_id = 1;

    // --- KROK 1: Vytvoření kampaně (draft) ---
    $create_url = 'https://api2.ecomailapp.cz/campaigns';
    $create_data = [
        'name'         => $data['subject'],
        'title'        => $data['subject'],
        'subject'      => $data['subject'],
       'recepient_lists' => [$list_id],
        'from_name'    => 'Postní kapky',
        'from_email'   => '2026@mail.postnikapky.cz',
        'reply_to'     => 'favnorovy@ado.cz',
        'html_content' => $html_content
    ];

    $create_response = wp_remote_post($create_url, [
        'method'    => 'POST',
        'headers'   => [ 'key' => $api_key, 'Content-Type'  => 'application/json; charset=utf-8' ],
        'body'      => json_encode($create_data),
        'timeout'   => 45,
    ]);

    if (is_wp_error($create_response) || wp_remote_retrieve_response_code($create_response) >= 400) {
        $error_message = is_wp_error($create_response) ? $create_response->get_error_message() : wp_remote_retrieve_body($create_response);
        return 'Ecomail KROK 1 (Vytvoření) CHYBA: ' . $error_message;
    }

    $create_body = json_decode(wp_remote_retrieve_body($create_response), true);
    $campaign_id = isset($create_body['id']) ? $create_body['id'] : null;

    if (!$campaign_id) {
        return 'Ecomail KROK 1 (Vytvoření) CHYBA: Nepodařilo se získat ID kampaně z odpovědi: ' . wp_remote_retrieve_body($create_response);
    }
    
    // --- KROK 2: Odeslání vytvořené kampaně ---
    $send_url = "https://api2.ecomailapp.cz/campaign/{$campaign_id}/send";

    $send_response = wp_remote_get($send_url, [
           'headers' => [
            'key'          => $api_key,
            'Content-Type' => 'application/json'
        ]
    ]);

    if (is_wp_error($send_response) || wp_remote_retrieve_response_code($send_response) >= 400) {
        $error_message = is_wp_error($send_response) ? $send_response->get_error_message() : wp_remote_retrieve_body($send_response);
        return "Ecomail KROK 2 (Odeslání) CHYBA pro kampaň ID {$campaign_id}: " . $error_message;
    }

    return "Ecomail ÚSPĚCH: Kampaň ID {$campaign_id} byla úspěšně odeslána. Odpověď serveru: " . wp_remote_retrieve_body($send_response);
}

/**
 * Manuální spouštěč pro odeslání testovacího e-mailu.
 */
function pehobr_test_ecomail_send() {
    $test_data = [
        'jpii_quote'      => 'Toto je testovací citát Jana Pavla II. určený pro ověření funkčnosti.',
        'benedict_quote'  => 'Toto je testovací citát Benedikta XVI.',
        'francis_quote'   => 'Toto je testovací citát papeže Františka.',
        'leo_quote'       => 'Toto je testovací citát Lva XIII.',
        'augustine_quote' => 'Toto je testovací citát svatého Augustina.',
        'prayer'          => 'Toto je testovací modlitba pro dnešní den.',
        'image_url'       => 'http://pkapky2026.local/wp-content/uploads/2025/06/what-ive-learned-from-road-trips-1.png',
        'subject'         => '[TEST] Denní postní kapka',
        'web_view_url'    => home_url('/nahled-emailu/?kapka_id=999'),
    ];

    $log_result = pehobr_trigger_ecomail_api_send($test_data);
    
    echo "<h1>Test odeslání do Ecomailu</h1>";
    echo "<p>Pokus o odeslání byl dokončen. Níže vidíte odpověď z Ecomail serveru.</p>";
    echo "<hr>";
    echo "<h2>Odpověď API:</h2>";
    echo "<pre>" . esc_html($log_result) . "</pre>";
    die();
}

/**
 * Spustí testovací odeslání, pokud je v URL parametr 'test_ecomail'.
 */
add_action('init', function() {
    if (isset($_GET['test_ecomail']) && $_GET['test_ecomail'] == 1) {
        if (current_user_can('manage_options')) {
            pehobr_test_ecomail_send();
        } else {
            wp_die('K této akci nemáte oprávnění.');
        }
    }
});