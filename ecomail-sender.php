<?php
/**
 * ZOBRAZIT HTML E-MAILU - ?zobrazit_html=1
 * TESTOVAT E-MAIL S REÁLNÝMI DATY - ?test_ecomail=1
 * Tento soubor obsahuje logiku pro odesílání e-mailů přes Ecomail.  
 * Logika pro odesílání e-mailů přes Ecomail.
 * VERZE 11: Změna 'citat_modlitba' na 'modlitba_text'.
 *
 * @package minimalistblogger-child
 */

// Zabráníme přímému přístupu k souboru
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Naplánuje denní úlohu pro odeslání.
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
 * Najde správnou "Denní kapku" a zahájí proces odeslání.
 */
function pehobr_find_and_send_daily_content() {
    $start_date_str = get_option('start_date_setting', '2026-02-18');
    try {
        $start_date = new DateTime($start_date_str, new DateTimeZone('Europe/Prague'));
        $today = new DateTime('today', new DateTimeZone('Europe/Prague'));

        if ($today < $start_date) {
            error_log('Ecomail Sender: Startovní datum ještě nenastalo.');
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
                // ZMĚNA ZDE: Používá se 'modlitba_text' místo 'citat_modlitba'
                $data_for_email = [
                    '*|JPII_QUOTE|*'      => get_post_meta(get_the_ID(), 'citat_janpavel', true),
                    '*|BENEDICT_QUOTE|*'  => get_post_meta(get_the_ID(), 'citat_benedikt', true),
                    '*|FRANCIS_QUOTE|*'   => get_post_meta(get_the_ID(), 'citat_frantisek', true),
                    '*|LEO_QUOTE|*'       => get_post_meta(get_the_ID(), 'citat_lev', true),
                    '*|AUGUSTINE_QUOTE|*' => get_post_meta(get_the_ID(), 'citat_augustin', true),
                    '*|PRAYER|*'          => get_post_meta(get_the_ID(), 'modlitba_text', true),
                    '*|IMAGE_URL|*'       => get_post_meta(get_the_ID(), 'image_url', true),
                    '*|SUBJECT|*'         => get_the_title(),
                ];
                $log_message = pehobr_trigger_ecomail_api_send($data_for_email);
                error_log($log_message);
            }
        } else {
            error_log('Ecomail Sender: Pro dnešní den nebyla nalezena žádná Denní kapka.');
        }
        wp_reset_postdata();
    } catch (Exception $e) {
        error_log('Chyba při přípravě denní kapky pro Ecomail: ' . $e->getMessage());
    }
}

/**
 * Sestaví HTML a odešle kampaň přes Ecomail.
 */
function pehobr_trigger_ecomail_api_send($data) {
    $template_path = get_stylesheet_directory() . '/static-email-template.html';
    if (!file_exists($template_path)) {
        return 'Ecomail chyba: Statická HTML šablona nebyla nalezena.';
    }

    $html_content = file_get_contents($template_path);

    foreach ($data as $placeholder => $value) {
        $processed_value = in_array($placeholder, ['*|JPII_QUOTE|*', '*|BENEDICT_QUOTE|*', '*|FRANCIS_QUOTE|*', '*|LEO_QUOTE|*', '*|AUGUSTINE_QUOTE|*', '*|PRAYER|*'])
            ? nl2br(esc_html($value))
            : esc_html($value);
        $html_content = str_replace($placeholder, $processed_value, $html_content);
    }
    
    $html_content = empty($data['*|IMAGE_URL|*'])
        ? preg_replace('/<img src="\*\|IMAGE_URL\|\*".*?>/i', '', $html_content)
        : str_replace('*|IMAGE_URL|*', esc_url($data['*|IMAGE_URL|*']), $html_content);

    $api_key = '5119581de3b3d06f4a0dda545a2ad5a4a3670e74e6a31cf069eff3f7d49b8ea2';
    $list_id = 1;

    $create_url = 'https://api2.ecomailapp.cz/campaigns';
    $create_data = [
        'name'            => $data['*|SUBJECT|*'],
        'title'           => $data['*|SUBJECT|*'],
        'subject'         => $data['*|SUBJECT|*'],
        'recepient_lists' => [$list_id],
        'from_name'       => 'Postní kapky',
        'from_email'      => '2026@mail.postnikapky.cz',
        'reply_to'        => 'favnorovy@ado.cz',
        'html_text'       => $html_content
    ];

    $create_response = wp_remote_post($create_url, [
        'method'    => 'POST',
        'headers'   => [ 'key' => $api_key, 'Content-Type'  => 'application/json; charset=utf-8' ],
        'body'      => json_encode($create_data),
        'timeout'   => 45,
    ]);

    if (is_wp_error($create_response) || wp_remote_retrieve_response_code($create_response) >= 400) {
        return 'Ecomail KROK 1 (Vytvoření kampaně) CHYBA: ' . (is_wp_error($create_response) ? $create_response->get_error_message() : wp_remote_retrieve_body($create_response));
    }

    $create_body = json_decode(wp_remote_retrieve_body($create_response), true);
    $campaign_id = $create_body['id'] ?? null;

    if (!$campaign_id) {
        return 'Ecomail KROK 1 (Vytvoření kampaně) CHYBA: Nepodařilo se získat ID kampaně.';
    }
    
    $send_url = "https://api2.ecomailapp.cz/campaign/{$campaign_id}/send";
    $send_response = wp_remote_get($send_url, [
           'headers' => [
            'key'          => $api_key,
            'Content-Type' => 'application/json'
        ]
    ]);

    if (is_wp_error($send_response) || wp_remote_retrieve_response_code($send_response) >= 400) {
        $error_message = is_wp_error($send_response) ? $send_response->get_error_message() : wp_remote_retrieve_body($send_response);
        return "Ecomail KROK 2 (Odeslání kampaně) CHYBA pro kampaň ID {$campaign_id}: " . $error_message;
    }

    return "Ecomail ÚSPĚCH: Kampaň ID {$campaign_id} byla úspěšně odeslána. Odpověď serveru: " . wp_remote_retrieve_body($send_response);
}

/**
 * Přidá možnost manuálního spuštění testů s reálnými daty.
 */
add_action('init', function() {
    if (!isset($_GET['test_ecomail']) && !isset($_GET['zobrazit_html'])) {
        return;
    }

    if (!current_user_can('manage_options')) {
        wp_die('K této akci nemáte oprávnění.');
    }

    // Načtení dat z poslední "Denní kapky" pro oba typy testů
    $args = array(
        'post_type'      => 'denni_kapka',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC', // Načteme nejnovější
    );
    $latest_kapka_query = new WP_Query($args);

    if ($latest_kapka_query->have_posts()) {
        $latest_kapka_query->the_post();
        // ZMĚNA ZDE: Používá se 'modlitba_text' místo 'citat_modlitba'
        $real_data = [
            '*|JPII_QUOTE|*'      => get_post_meta(get_the_ID(), 'citat_janpavel', true),
            '*|BENEDICT_QUOTE|*'  => get_post_meta(get_the_ID(), 'citat_benedikt', true),
            '*|FRANCIS_QUOTE|*'   => get_post_meta(get_the_ID(), 'citat_frantisek', true),
            '*|LEO_QUOTE|*'       => get_post_meta(get_the_ID(), 'citat_lev', true),
            '*|AUGUSTINE_QUOTE|*' => get_post_meta(get_the_ID(), 'citat_augustin', true),
            '*|PRAYER|*'          => get_post_meta(get_the_ID(), 'modlitba_text', true),
            '*|IMAGE_URL|*'       => get_post_meta(get_the_ID(), 'image_url', true),
            '*|SUBJECT|*'         => '[REAL-DATA TEST] ' . get_the_title(), // Přidáme identifikátor testu
        ];
        wp_reset_postdata();
    } else {
        wp_die('Chyba: V systému nebyla nalezena žádná "Denní kapka" pro provedení testu.');
    }

    // Odeslání testovacího e-mailu s reálnými daty
    if (isset($_GET['test_ecomail']) && $_GET['test_ecomail'] == 1) {
        $log_result = pehobr_trigger_ecomail_api_send($real_data);
        echo "<h1>Test odeslání do Ecomailu (s reálnými daty)</h1><p>Pokus o odeslání byl dokončen.</p><hr><h2>Odpověď API:</h2><pre>" . esc_html($log_result) . "</pre>";
        die();
    }

    // Zobrazení HTML náhledu s reálnými daty
    if (isset($_GET['zobrazit_html']) && $_GET['zobrazit_html'] == 1) {
        $template_path = get_stylesheet_directory() . '/static-email-template.html';
        if (!file_exists($template_path)) {
            wp_die('Chyba: Statická HTML šablona nebyla nalezena.');
        }

        $html_content = file_get_contents($template_path);
        
        foreach ($real_data as $placeholder => $value) {
             $processed_value = in_array($placeholder, ['*|JPII_QUOTE|*', '*|BENEDICT_QUOTE|*', '*|FRANCIS_QUOTE|*', '*|LEO_QUOTE|*', '*|AUGUSTINE_QUOTE|*', '*|PRAYER|*'])
                ? nl2br(esc_html($value))
                : esc_html($value);
            $html_content = str_replace($placeholder, $processed_value, $html_content);
        }
        
        $html_content = empty($real_data['*|IMAGE_URL|*'])
            ? preg_replace('/<img src="\*\|IMAGE_URL\|\*".*?>/i', '', $html_content)
            : str_replace('*|IMAGE_URL|*', esc_url($real_data['*|IMAGE_URL|*']), $html_content);

        echo '<!DOCTYPE html><html><head><title>Náhled HTML e-mailu (reálná data)</title><meta charset="UTF-8"></head><body>';
        echo '<h1>Vygenerovaný HTML kód e-mailu</h1><p>Níže vidíte přesný HTML kód, který by byl odeslán do Ecomailu. Data jsou z nejnovější Denní kapky.</p><hr>';
        echo '<h2>Kód pro vložení do Ecomailu:</h2><textarea style="width: 100%; height: 400px; font-family: monospace;">' . esc_html($html_content) . '</textarea>';
        echo '<hr><h2>Vizuální náhled:</h2>' . $html_content;
        echo '</body></html>';
        die();
    }
});