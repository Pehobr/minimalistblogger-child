<?php
/**
 * Template Name: Úvodní stránka aplikace Home
 * Description: Speciální úvodní stránka, která dynamicky načítá denní obsah.
 * VERZE 24: Odstranění popisků u třetího řádku ikon (Modlitba, Bible, Inspirace).
 * @package minimalistblogger-child
 */

get_header();

// --- Logika pro načítání denního obsahu ---
$page_id_for_defaults = get_the_ID();
$quotes = [];
$nazev_dne = '';
$datum_dne = '';
$daily_post_id = null;

$start_date_str = get_option('start_date_setting', '2026-02-18');
try {
    $start_date = new DateTime($start_date_str, new DateTimeZone('Europe/Prague'));
    $today = new DateTime('today', new DateTimeZone('Europe/Prague'));
    if ($today >= $start_date) {
        $interval = $start_date->diff($today);
        $day_offset = $interval->days;
        $args = ['post_type' => 'denni_kapka', 'post_status' => 'publish', 'posts_per_page' => 1, 'offset' => $day_offset, 'orderby' => 'date', 'order' => 'ASC'];
        $daily_query = new WP_Query($args);
        if ($daily_query->have_posts()) {
            $daily_query->the_post();
            $daily_post_id = get_the_ID();
            $nazev_dne = get_post_meta($daily_post_id, 'nazev_dne', true);
            $datum_dne = get_post_meta($daily_post_id, 'datum_dne', true);
            wp_reset_postdata();
        }
    }
} catch (Exception $e) { $daily_post_id = null; }

if ( empty($daily_post_id) ) {
    $fallback_args = ['post_type' => 'denni_kapka', 'post_status' => 'publish', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC'];
    $fallback_query = new WP_Query($fallback_args);
    if ($fallback_query->have_posts()) {
        $fallback_query->the_post();
        $daily_post_id = get_the_ID();
        $nazev_dne = get_post_meta($daily_post_id, 'nazev_dne', true);
        $datum_dne = get_post_meta($daily_post_id, 'datum_dne', true);
        wp_reset_postdata();
    }
}

$grid_items = [
    ['name' => 'Sv. Jan Pavel II.', 'slug' => 'papez-frantisek', 'icon' => 'ikona-janpavel.png', 'citat_key' => 'citat_janpavel', 'label' => 'Jan Pavel', 'type' => 'text'],
    ['name' => 'Papež Benedikt XVI.', 'slug' => 'papez-benedikt', 'icon' => 'ikona-benedikt.png', 'citat_key' => 'citat_benedikt', 'label' => 'Benedikt', 'type' => 'text'],
    ['name' => 'Papež František', 'slug' => 'papez-frantisek', 'icon' => 'ikona-frantisek.png', 'citat_key' => 'citat_frantisek', 'label' => 'František', 'type' => 'text'],
    ['name' => 'Sv. Augustin', 'slug' => '#', 'icon' => 'ikona-augustin.png', 'citat_key' => 'citat_augustin', 'label' => 'Augustin', 'type' => 'image'],
    ['name' => 'Papež Lev XIV.', 'slug' => 'papez-lev', 'icon' => 'ikona-lev.png', 'citat_key' => 'citat_lev', 'label' => 'Lev XIV.', 'type' => 'text'],
    ['name' => 'Modlitba', 'slug' => 'modlitba', 'icon' => 'ikona-modlitba.png', 'citat_key' => 'modlitba_text', 'audio_key' => 'modlitba_url', 'label' => 'Modlitba', 'type' => 'text'],
    ['name' => 'Bible', 'slug' => 'poboznosti', 'icon' => 'ikona-bible.png', 'label' => 'Bible', 'type' => 'text'],
    ['name' => 'Inspirace', 'slug' => 'svatost', 'icon' => 'ikona-inspirace.png', 'citat_key' => 'video_inspirace_embed', 'label' => 'Inspirace', 'type' => 'video'],
];

$library_items = [
    ['name' => 'Video', 'icon' => 'knihovna-video.png', 'url' => '/video-kapky/'],
    ['name' => 'Audio', 'icon' => 'knihovna-audio.png', 'url' => '/postni-pisne/'],
    ['name' => 'Rádio', 'icon' => 'knihovna-radio.png', 'url' => '/krestanska-radia'],
    ['name' => 'Podcast', 'icon' => 'knihovna-podcast.png', 'url' => '/podcast'],
];

foreach ($grid_items as $item) {
    if (isset($item['citat_key'])) {
        $source_post_id = $daily_post_id ?? $page_id_for_defaults;
        $quotes[$item['citat_key']] = get_post_meta($source_post_id, $item['citat_key'], true);
    }
}
?>

<div id="primary" class="featured-content content-area intro-app">
    <main id="main" class="site-main">
        <div id="intro-wrapper">
            
            <?php if (!empty($nazev_dne) || !empty($datum_dne)): ?>
                <div id="daily-info-container">
                    <?php if (!empty($nazev_dne)): ?><h2 id="daily-info-title" class="daily-info-item"><?php echo esc_html($nazev_dne); ?></h2><?php endif; ?>
                    <?php if (!empty($datum_dne)): ?><p id="daily-info-date" class="daily-info-item"><?php echo esc_html($datum_dne); ?></p><?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="pope-section-container">
                <div class="pope-items-wrapper">
                    <?php for ($i = 0; $i < 3; $i++): $item = $grid_items[$i]; $content_html = isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : ''; $has_content = !empty($content_html); $link_url = $has_content ? '#' : home_url('/' . $item['slug'] . '/'); ?>
                        <div class="pope-item">
                            <a href="<?php echo esc_url($link_url); ?>" class="pope-icon-link" <?php if ($has_content): ?>data-target-id="quote-content-<?php echo esc_attr($item['citat_key']); ?>" data-type="<?php echo esc_attr($item['type']); ?>" data-author-name="<?php echo esc_attr($item['name']); ?>"<?php endif; ?>>
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                            </a>
                            <span class="grid-item-label"><?php echo esc_html($item['label']); ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="saints-section-container">
                <div class="saints-items-wrapper">
                    <div class="saints-item-boxed">
                        <?php $augustin_item = $grid_items[3]; $augustin_content = isset($quotes[$augustin_item['citat_key']]) ? $quotes[$augustin_item['citat_key']] : ''; $augustin_has_content = !empty($augustin_content); $augustin_link_url = $augustin_has_content ? '#' : home_url('/' . $augustin_item['slug'] . '/'); ?>
                        <a href="<?php echo esc_url($augustin_link_url); ?>" class="pope-icon-link" <?php if ($augustin_has_content): ?>data-target-id="quote-content-<?php echo esc_attr($augustin_item['citat_key']); ?>" data-type="<?php echo esc_attr($augustin_item['type']); ?>" data-author-name="<?php echo esc_attr($augustin_item['name']); ?>"<?php endif; ?>>
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-augustin.png'); ?>" alt="Augustin">
                        </a>
                        <span class="grid-item-label">Augustin</span>
                    </div>
                    <div class="saints-item-center">
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/erb-lev.png'); ?>" alt="Vatikán">
                    </div>
                    <div class="saints-item-boxed">
                         <a href="<?php echo esc_url(home_url('/papez-lev/')); ?>">
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-lev.png'); ?>" alt="Lev XIV.">
                        </a>
                        <span class="grid-item-label">Lev XIV.</span>
                    </div>
                </div>
            </div>

            <div class="third-row-section-container">
                <div class="third-row-items-wrapper">
                    <?php for ($i = 5; $i < count($grid_items); $i++): $item = $grid_items[$i]; $content_html = isset($item['citat_key']) && isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : ''; $has_content = !empty($content_html); $link_url = $has_content ? '#' : home_url('/' . $item['slug'] . '/'); ?>
                        <div class="grid-item-wrapper">
                            <a href="<?php echo esc_url($link_url); ?>" class="icon-grid-item" <?php if ($has_content): ?>data-target-id="quote-content-<?php echo esc_attr($item['citat_key']); ?>" data-type="<?php echo esc_attr($item['type']); ?>" data-author-name="<?php echo esc_attr($item['name']); ?>"<?php endif; ?>>
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                            </a>
                            <?php // Popisek je nyní odstraněn ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div id="library-grid-container">
                <?php foreach ($library_items as $item) : ?>
                    <div class="library-item-wrapper">
                        <a href="<?php echo esc_url(home_url($item['url'])); ?>" class="library-grid-item">
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<?php get_sidebar(); ?>

<div id="hidden-quotes-container" style="display: none; visibility: hidden;">
    <?php
    foreach ($grid_items as $item) :
        if (isset($item['citat_key'])) {
            $content_html = isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
            if (!empty($content_html)) :
                $modal_content = '';

                if ($item['type'] === 'image') {
                    $modal_content = '<img src="' . esc_url($content_html) . '" alt="' . esc_attr($item['name']) . '">';
                } elseif ($item['type'] === 'video') {
                     $modal_content = $content_html; // Iframe je již v obsahu
                } else {
                    $modal_content = wpautop($content_html);
                    // Speciální případ pro modlitbu s audio přehrávačem
                    if (isset($item['audio_key'])) {
                        $audio_url = get_post_meta($daily_post_id ?? $page_id_for_defaults, $item['audio_key'], true);
                        if (!empty($audio_url)) {
                             $modal_content .= '<div class="modal-audio-player" data-audio-src="' . esc_url($audio_url) . '"><div class="map-top-row"><button class="map-play-pause-btn" aria-label="Přehrát / Pauza"><i class="fa fa-play" aria-hidden="true"></i></button><div class="map-time-display"><span class="map-current-time">0:00</span><span class="map-time-divider">/</span><span class="map-duration">0:00</span></div></div><div class="map-slider-wrapper"><input type="range" class="map-seek-slider" value="0" min="0" max="100" step="0.1"></div></div>';
                        }
                    }
                }
                ?>
                <div id="quote-content-<?php echo esc_attr($item['citat_key']); ?>">
                    <?php echo $modal_content; ?>
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

<div id="quote-modal-container" class="quote-modal-container">
    <button id="quote-modal-favorite-btn" class="quote-modal-favorite-btn"><i class="fa fa-star-o"></i></button>
    <button id="quote-modal-close-btn" class="quote-modal-close-btn">×</button>
    <div id="quote-modal-content" class="quote-modal-content"></div>
</div>

<div id="donation-popup-overlay"></div>
<a href="<?php echo esc_url( home_url('/prosba/') ); ?>" id="donation-popup-container">
    <span>Děkujeme<br>za Vaši podporu</span>
    <span id="donation-timer">7s</span> 
</a>
<?php get_footer(); ?>