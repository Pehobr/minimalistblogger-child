<?php
/**
 * Template Name: Úvodní stránka aplikace Home
 * Description: Speciální úvodní stránka, která dynamicky načítá denní obsah.
 * VERZE 11: Doplnění popisků pod ikony bez boxu.
 * @package minimalistblogger-child
 */

get_header();

// --- Logika pro načítání denního obsahu ---

$page_id_for_defaults = get_the_ID();
$quotes = [];
$nazev_dne = '';
$datum_dne = '';
$daily_post_id = null; // Inicializace pro případ, že se nenajde denní kapka

// Načtení startovního data z nastavení WordPressu
$start_date_str = get_option( 'start_date_setting', '2026-02-18' );

try {
    $start_date = new DateTime($start_date_str, new DateTimeZone('Europe/Prague'));
    $today = new DateTime('today', new DateTimeZone('Europe/Prague'));
    
    // Zobrazíme obsah, pouze pokud je dnešní datum větší nebo rovno startovnímu datu
    if ($today >= $start_date) {
        $interval = $start_date->diff($today);
        $day_offset = $interval->days;

        // Dotaz na nalezení správného příspěvku typu 'denni_kapka'
        $args = array(
            'post_type' => 'denni_kapka',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'offset' => $day_offset,
            'orderby' => 'date',
            'order' => 'ASC',
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
} catch (Exception $e) {
    // V případě chyby s datem se nepřiřadí žádný denní příspěvek
    $daily_post_id = null;
}

// --- Definice dlaždic na úvodní stránce ---
$grid_items = [
    ['name' => 'Sv. Jan Pavel II.', 'slug' => 'papez-frantisek', 'icon' => 'ikona-janpavel.png', 'citat_key' => 'citat_janpavel', 'label' => 'Jan Pavel', 'type' => 'text'],
    ['name' => 'Papež Benedikt XVI.', 'slug' => 'papez-benedikt', 'icon' => 'ikona-benedikt.png', 'citat_key' => 'citat_benedikt', 'label' => 'Benedikt', 'type' => 'text'],
    ['name' => 'Papež František', 'slug' => 'papez-frantisek', 'icon' => 'ikona-frantisek.png', 'citat_key' => 'citat_frantisek', 'label' => 'František', 'type' => 'text'],
    ['name' => 'Sv. Augustin', 'slug' => 'nabozenske-texty', 'icon' => 'ikona-augustin.png', 'citat_key' => 'citat_augustin', 'label' => 'Augustin', 'type' => 'text'],
    ['name' => 'Papež Lev XIII.', 'slug' => 'papez-lev', 'icon' => 'ikona-lev.png', 'citat_key' => 'citat_lev', 'label' => 'Lev XIII.', 'type' => 'text'],
    ['name' => 'Modlitba', 'slug' => 'modlitba', 'icon' => 'ikona-modlitba.png', 'citat_key' => 'modlitba_text', 'audio_key' => 'modlitba_url', 'label' => 'Modlitba', 'type' => 'text'],
    ['name' => 'Bible', 'slug' => 'citaty', 'icon' => 'ikona-bible.png', 'citat_key' => 'audio_bible_url', 'label' => 'Bible', 'type' => 'audio'],
    ['name' => 'Inspirace', 'slug' => 'svatost', 'icon' => 'ikona-inspirace.png', 'citat_key' => 'video_inspirace_embed', 'label' => 'Inspirace', 'type' => 'video'],
];

// --- Načtení obsahu pro jednotlivé dlaždice ---
foreach ($grid_items as $item) {
    if (isset($item['citat_key'])) {
        $citat_key = $item['citat_key'];
        // Pokud máme ID denní kapky, použijeme ho. Jinak se použijí výchozí hodnoty ze stránky.
        $source_post_id = $daily_post_id ?? $page_id_for_defaults;

        // Speciální logika pro "Modlitbu", která má text i audio
        if (isset($item['audio_key'])) {
            $audio_key = $item['audio_key'];
            $text_content = get_post_meta($source_post_id, $citat_key, true);
            $audio_url = get_post_meta($source_post_id, $audio_key, true);
            
            $combined_content = '';
            if (!empty($text_content)) {
                $combined_content = wpautop($text_content); // Formátuje text do odstavců
                
                // Pokud je k dispozici URL audia, přidá se vlastní audio přehrávač
                if (!empty($audio_url)) {
                    $combined_content .= '
                        <div class="modal-audio-player" data-audio-src="' . esc_url($audio_url) . '">
                            <div class="map-top-row">
                                <button class="map-play-pause-btn" aria-label="Přehrát / Pauza">
                                    <i class="fa fa-play" aria-hidden="true"></i>
                                </button>
                                <div class="map-time-display">
                                    <span class="map-current-time">0:00</span>
                                    <span class="map-time-divider">/</span>
                                    <span class="map-duration">0:00</span>
                                </div>
                            </div>
                            <div class="map-slider-wrapper">
                                <input type="range" class="map-seek-slider" value="0" min="0" max="100" step="0.1">
                            </div>
                        </div>';
                }
            }
            $quotes[$citat_key] = $combined_content;
        } else {
            // Standardní logika pro ostatní dlaždice
            $quotes[$citat_key] = get_post_meta($source_post_id, $citat_key, true);
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
                foreach ($grid_items as $index => $item) :
                    $content_html = isset($item['citat_key'], $quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
                    $has_content = !empty($content_html);
                    $link_url = $has_content ? '#' : home_url('/' . $item['slug'] . '/');
                    
                    // Logika, která přidá třídu .image-only prvním pěti položkám
                    $is_image_only = $index < 5;
                    $wrapper_class = 'grid-item-wrapper' . ($is_image_only ? ' image-only' : '');
                ?>
                    <div class="<?php echo esc_attr($wrapper_class); ?>">
                        <a href="<?php echo esc_url($link_url); ?>"
                           class="icon-grid-item"
                           <?php if ($has_content) : ?>
                               data-target-id="quote-content-<?php echo esc_attr($item['citat_key']); ?>"
                               data-type="<?php echo esc_attr($item['type']); ?>"
                               data-author-name="<?php echo esc_attr($item['name']); ?>"
                           <?php endif; ?>
                        >
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                        </a>
                        <?php // Podmínka pro zobrazení popisku byla odstraněna, zobrazí se vždy ?>
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
            $content_html = isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
            if (!empty($content_html)) :
                // Definice povolených HTML tagů a atributů pro bezpečné vypsání
                $allowed_html = [
                    'p' => ['style' => []], 'em' => [], 'strong' => [], 'br' => [],
                    'iframe' => ['width' => [], 'height' => [], 'src' => [], 'title' => [], 'frameborder' => [], 'allow' => [], 'allowfullscreen' => [], 'referrerpolicy' => []],
                    // Povolené tagy pro vlastní audio přehrávač
                    'div' => ['class' => [], 'data-audio-src' => []],
                    'button' => ['class' => [], 'aria-label' => []],
                    'i' => ['class' => [], 'aria-hidden' => []],
                    'span' => ['class' => [], 'style' => []],
                    'input' => ['type' => [], 'class' => [], 'value' => [], 'min' => [], 'max' => [], 'step' => []],
                    // Ponecháno pro standardní audio (např. Bible)
                    'audio' => ['controls' => [], 'src' => [], 'style' => []]
                ];
                
                $modal_content = $content_html;

                // Zvláštní ošetření pro standardní audio
                if ($item['type'] === 'audio' && $item['citat_key'] !== 'modlitba_text') {
                    $modal_content = '<audio controls src="' . esc_url($content_html) . '">Váš prohlížeč nepodporuje přehrávání audia.</audio>';
                }
                ?>
                <div id="quote-content-<?php echo esc_attr($item['citat_key']); ?>">
                    <?php echo wp_kses($modal_content, $allowed_html); ?>
                </div>
                <?php
            endif;
        }
    endforeach;
    ?>
</div>

<div id="quote-modal-overlay" class="quote-modal-overlay"></div>
<div id="quote-modal-container" class="quote-modal-container">
    <button id="quote-modal-favorite-btn" class="quote-modal-favorite-btn"><i class="fa fa-star-o"></i></button>
    <button id="quote-modal-close-btn" class="quote-modal-close-btn">×</button>
    <div id="quote-modal-content" class="quote-modal-content"></div>
</div>

<?php get_footer(); ?>