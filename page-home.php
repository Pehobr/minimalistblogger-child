<?php
/**
 * Template Name: Úvodní stránka aplikace Home
 * Description: Speciální úvodní stránka, která dynamicky načítá denní obsah.
 *
 * @package minimalistblogger-child
 */

get_header();

// --- Logika pro načítání denního obsahu ---

$page_id_for_defaults = get_the_ID();
$quotes = [];
$nazev_dne = ''; 
$datum_dne = ''; 

$start_date_str = get_option( 'start_date_setting', '2026-02-18' );
try {
    $start_date = new DateTime($start_date_str, new DateTimeZone('Europe/Prague'));
    $today = new DateTime('today', new DateTimeZone('Europe/Prague'));
    if ($today >= $start_date) {
        $interval = $start_date->diff($today);
        $day_offset = $interval->days;
        $args = array(
            'post_type' => 'denni_kapka', 'post_status' => 'publish', 'posts_per_page' => 1,
            'offset' => $day_offset, 'orderby' => 'date', 'order' => 'ASC',
        );
        $daily_query = new WP_Query($args);
        if ($daily_query->have_posts()) {
            while ($daily_query->have_posts()) {
                $daily_query->the_post();
                $daily_post_id = get_the_ID();
                $nazev_dne = get_post_meta($daily_post_id, 'nazev_dne', true);
                $datum_dne = get_post_meta($daily_post_id, 'datum_dne', true);
            }
            wp_reset_postdata();
        }
    }
} catch (Exception $e) { $daily_post_id = null; }

// --- Seznam dlaždic s novými popisky ---
$grid_items = [
    ['name' => 'Sv. Jan Pavel II.', 'slug' => 'papez-frantisek', 'icon' => 'ikona-janpavel.png', 'citat_key' => 'citat_janpavel', 'label' => 'Jan Pavel'],
    ['name' => 'Papež Benedikt XVI.', 'slug' => 'papez-benedikt', 'icon' => 'ikona-benedikt.png', 'citat_key' => 'citat_benedikt', 'label' => 'Benedikt'],
    ['name' => 'Papež František', 'slug' => 'papez-frantisek', 'icon' => 'ikona-frantisek.png', 'citat_key' => 'citat_frantisek', 'label' => 'František'],
    ['name' => 'Augustin', 'slug' => 'nabozenske-texty', 'icon' => 'ikona-augustin.png', 'citat_key' => 'citat_augustin', 'label' => 'Augustin'],
    ['name' => 'Papež Lev XIII.', 'slug' => 'papez-lev', 'icon' => 'ikona-lev.png', 'citat_key' => 'citat_lev', 'label' => 'Lev XIII.'],
    ['name' => 'Modlitba', 'slug' => 'modlitba', 'icon' => 'ikona-modlitba.png', 'citat_key' => 'citat_modlitba', 'label' => 'Modlitba'],
    ['name' => 'Text 1', 'slug' => 'citaty', 'icon' => 'ikona-bible.png', 'citat_key' => 'citat_text1', 'label' => 'Bible'],
    ['name' => 'Text 2', 'slug' => 'svatost', 'icon' => 'ikona-inspirace.png', 'citat_key' => 'citat_text2', 'label' => 'Inspirace'],
];

foreach ($grid_items as $item) {
    if (isset($item['citat_key'])) {
        $citat_key = $item['citat_key'];
        if (isset($daily_post_id)) {
            $quotes[$citat_key] = get_post_meta($daily_post_id, $citat_key, true);
        } else {
            $quotes[$citat_key] = get_post_meta($page_id_for_defaults, $citat_key, true);
        }
    }
}
?>

<div id="primary" class="featured-content content-area intro-app">
    <main id="main" class="site-main">
        <div id="intro-wrapper">
            
            <?php if ( ! empty( $nazev_dne ) || ! empty( $datum_dne ) ) : ?>
                <div id="daily-info-container">
                    <?php if ( ! empty( $nazev_dne ) ) : ?>
                        <h2 id="daily-info-title"><?php echo esc_html( $nazev_dne ); ?></h2>
                    <?php endif; ?>
                    <?php if ( ! empty( $datum_dne ) ) : ?>
                        <p id="daily-info-date"><?php echo esc_html( $datum_dne ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div id="intro-grid-container">
                <?php
                // Smyčka pro generování 8 boxů s popisky
                foreach ($grid_items as $item) :
                    $quote_html = isset($item['citat_key']) && isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
                    $has_quote = !empty($quote_html);
                    $link_url = $has_quote ? '#' : home_url('/' . $item['slug'] . '/');
                ?>
                    <div class="grid-item-wrapper">
                        <a href="<?php echo esc_url($link_url); ?>" 
                           class="icon-grid-item"
                           <?php if ($has_quote) : ?>
                               data-target-id="quote-content-<?php echo esc_attr($item['citat_key']); ?>"
                           <?php endif; ?>
                        >
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                        </a>
                        <span class="grid-item-label"><?php echo esc_html($item['label']); ?></span>
                    </div>
                <?php endforeach; ?>

                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/erb-augustin.png'); ?>" alt="Erb Augustiniánů" id="erb-augustin" class="grid-erb">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/erb-lev.png'); ?>" alt="Erb Papeže Lva XIII." id="erb-lev" class="grid-erb">
            </div>
        </div>
    </main>
</div>

<div id="hidden-quotes-container" style="display: none; visibility: hidden;">
    <?php
    foreach ($grid_items as $item) :
        if (isset($item['citat_key'])) {
            $quote_html = isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
            if (!empty($quote_html)) :
                $allowed_html = ['p' => ['style' => []], 'em' => [], 'strong' => [], 'br' => [], 'span' => ['style' => []]];
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