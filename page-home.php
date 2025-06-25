<?php
/**
 * Template Name: Úvodní stránka aplikace Home
 * Description: Speciální úvodní stránka, která dynamicky načítá denní obsah.
 *
 * @package minimalistblogger-child
 */

get_header();

// --- ZDE ZAČÍNÁ NOVÁ LOGIKA PRO NAČÍTÁNÍ DENNÍHO OBSAHU ---

$page_id_for_defaults = get_the_ID();
$quotes = []; // Připravíme si prázdné pole pro citáty

// --- NASTAVENÍ CYKLU ---
// Zde nastavte datum Popeleční středy pro daný rok
$start_date_str = '2026-02-18'; // POPELEČNÍ STŘEDA 2026

try {
    $start_date = new DateTime($start_date_str, new DateTimeZone('Europe/Prague'));
    $today = new DateTime('today', new DateTimeZone('Europe/Prague'));

    // Zjistíme, jestli jsme v období cyklu
    if ($today >= $start_date) {
        $interval = $start_date->diff($today);
        $day_offset = $interval->days;

        // Argumenty pro nalezení správného příspěvku v "Denních kapkách"
        $args = array(
            'post_type'      => 'denni_kapka', // Náš nový CPT
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
                // Získáme ID nalezeného denního příspěvku
                $daily_post_id = get_the_ID();
            }
            wp_reset_postdata();
        }
    }

} catch (Exception $e) {
    // V případě chyby v datu se nic nestane, použijí se výchozí hodnoty
    $daily_post_id = null;
}

// === ZMĚNA ZDE: AKTUALIZOVANÝ SEZNAM DLAŽDIC A KLÍČŮ PRO CITÁTY ===
$grid_items = [
    ['name' => 'Sv. Jan Pavel II.', 'slug' => 'papez-frantisek', 'icon' => 'ikona-janpavel.png', 'citat_key' => 'citat_janpavel'],
    ['name' => 'Papež Benedikt XVI.', 'slug' => 'papez-benedikt', 'icon' => 'ikona-benedikt.png', 'citat_key' => 'citat_benedikt'],
    ['name' => 'Papež František', 'slug' => 'papez-frantisek', 'icon' => 'ikona-frantisek.png', 'citat_key' => 'citat_frantisek'],
    ['name' => 'Augustin', 'slug' => 'nabozenske-texty', 'icon' => 'ikona-augustin.png', 'citat_key' => 'citat_augustin'],
    ['name' => 'Papež Lev XIII.', 'slug' => 'papez-lev', 'icon' => 'ikona-lev.png', 'citat_key' => 'citat_lev'],
    ['name' => 'Modlitba', 'slug' => 'modlitba', 'icon' => 'ikona-modlitba.png', 'citat_key' => 'citat_modlitba'],
    ['name' => 'Text 1', 'slug' => 'citaty', 'icon' => 'ikona-citaty.png', 'citat_key' => 'citat_text1'],
    ['name' => 'Text 2', 'slug' => 'svatost', 'icon' => 'ikona-svatost.png', 'citat_key' => 'citat_text2'],
    ['name' => 'Komunita', 'slug' => 'komunita', 'icon' => 'ikona-komunita.png'], // Tato dlaždice již nemá 'citat_key'
];


// Naplníme pole citátů buď z denního příspěvku, nebo z výchozích hodnot na stránce
foreach ($grid_items as $item) {
    // Přidáváme citát jen pokud pro dlaždici existuje klíč
    if (isset($item['citat_key'])) {
        $citat_key = $item['citat_key'];
        // Pokud máme ID denního příspěvku, použijeme ho
        if (isset($daily_post_id)) {
            $quotes[$citat_key] = get_post_meta($daily_post_id, $citat_key, true);
        } else {
            // Jinak použijeme výchozí hodnoty zadané na stránce "Home"
            $quotes[$citat_key] = get_post_meta($page_id_for_defaults, $citat_key, true);
        }
    }
}

// --- KONEC NOVÉ LOGIKY ---
?>

<div id="primary" class="featured-content content-area intro-app">
    <main id="main" class="site-main">

        <div id="intro-wrapper">
            <div id="intro-grid-container">
                <?php
                foreach ($grid_items as $item) :
                    // Zkontrolujeme, zda pro danou dlaždici existuje citát
                    $quote_html = isset($item['citat_key']) && isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
                    $has_quote = !empty($quote_html);
                    $link_url = $has_quote ? '#' : home_url('/' . $item['slug'] . '/');
                ?>
                    <a href="<?php echo esc_url($link_url); ?>" 
                       class="icon-grid-item"
                       <?php if ($has_quote) : ?>
                           data-target-id="quote-content-<?php echo esc_attr($item['citat_key']); ?>"
                       <?php endif; ?>
                    >
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </main>
</div>

<div id="hidden-quotes-container" style="display: none; visibility: hidden;">
    <?php
    foreach ($grid_items as $item) :
        // Vytvoříme skrytý div jen pokud pro dlaždici existuje citát
        if (isset($item['citat_key'])) {
            $quote_html = isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
            if (!empty($quote_html)) :
                $allowed_html = [
                    'p' => ['style' => []], 'em' => [], 'strong' => [], 'br' => [], 'span' => ['style' => []],
                ];
                ?>
                <div id="quote-content-<?php echo esc_attr($item['citat_key']); ?>">
                    <?php echo wp_kses($quote_html, $allowed_html); ?>
                </div>
                <?php
            endif;
        }
    endforeach;
    ?>
</div>


<div id="quote-modal-overlay" class="quote-modal-overlay"></div>
<div id="quote-modal-container" class="quote-modal-container">
    <button id="quote-modal-close-btn" class="quote-modal-close-btn">&times;</button>
    <div id="quote-modal-content" class="quote-modal-content"></div>
</div>

<?php get_footer(); ?>