<?php
/**
 * Template Name: Úvodní stránka aplikace Home
 * Description: Speciální úvodní stránka, která dynamicky načítá denní obsah a řadí sekce podle nastavení.
 * VERZE 45: Oprava odkazu u ikony Modlitba na přímý odkaz.
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
$source_post_id = $daily_post_id ?? $page_id_for_defaults;

// === DEFINICE SEKCÍ (s úpravou pro Inspiraci) ===
$grid_items = [
    ['name' => 'Sv. Jan Pavel II.', 'slug' => 'papez-frantisek', 'icon' => 'ikona-janpavel.png', 'citat_key' => 'citat_janpavel', 'label' => 'sv. Jan Pavel II.', 'type' => 'text'],
    ['name' => 'Papež Benedikt XVI.', 'slug' => 'papez-benedikt', 'icon' => 'ikona-benedikt.png', 'citat_key' => 'citat_benedikt', 'label' => 'Benedikt XVI.', 'type' => 'text'],
    ['name' => 'Papež František', 'slug' => 'papez-frantisek', 'icon' => 'ikona-frantisek.png', 'citat_key' => 'citat_frantisek', 'label' => 'František', 'type' => 'text'],
    ['name' => 'Sv. Augustin', 'slug' => '#', 'icon' => 'ikona-augustin.png', 'foto_key' => 'foto_url', 'label' => 'sv. Augustin', 'type' => 'image'],
    ['name' => 'Papež Lev XIV.', 'slug' => 'papez-lev', 'icon' => 'ikona-lev.png', 'citat_key' => 'citat_lev', 'label' => 'Lev XIV.', 'type' => 'text'],
    ['name' => 'Modlitba', 'slug' => 'modlitba', 'icon' => 'ikona-modlitba.png', 'light_icon' => 'ikona-modlitba-svetla.png', 'citat_key' => 'modlitba_text', 'audio_key' => 'modlitba_url', 'label' => 'Modlitba', 'type' => 'text'],
    ['name' => 'Bible', 'slug' => 'poboznosti', 'icon' => 'ikona-bible.png', 'light_icon' => 'ikona-bible-svetla.png', 'label' => 'Bible', 'type' => 'text'],
    ['name' => 'Inspirace', 'slug' => '#', 'icon' => 'ikona-inspirace.png', 'light_icon' => 'ikona-inspirace-svetla.png', 'citat_key' => 'ai_inspiration_placeholder', 'label' => 'Inspirace', 'type' => 'ai_inspiration'],
];
$library_items = [
    ['name' => 'Video', 'icon' => 'knihovna-video.png', 'light_icon' => 'knihovna-video2.png', 'url' => '/video-kapky/'],
    ['name' => 'Audio', 'icon' => 'knihovna-audio.png', 'light_icon' => 'knihovna-audio2.png', 'url' => '/postni-pisne/'],
    ['name' => 'Rádio', 'icon' => 'knihovna-radio.png', 'light_icon' => 'knihovna-radio2.png', 'url' => '/krestanska-radia'],
    ['name' => 'Podcast', 'icon' => 'knihovna-podcast.png', 'light_icon' => 'knihovna-podcast2.png', 'url' => '/podcast'],
];
$desktop_nav_items = [
    ['name' => 'Oblíbené', 'fa_icon' => 'fa-star', 'url' => '/oblibene-texty/'],
    ['name' => 'Archiv', 'fa_icon' => 'fa-folder-open-o', 'url' => '/archiv-citatu/'],
    ['name' => 'Návody', 'fa_icon' => 'fa-cubes', 'url' => '/navody/'],
    ['name' => 'Fotogalerie', 'fa_icon' => 'fa-picture-o', 'url' => '/fotogalerie/'],
    ['name' => 'Zápisník', 'fa_icon' => 'fa-pencil', 'url' => '/zapisnik/'],
];

// Načtení dat
foreach ($grid_items as $item) {
    if (isset($item['citat_key'])) {
        $quotes[$item['citat_key']] = get_post_meta($source_post_id, $item['citat_key'], true);
    }
}
$augustin_photo_url = get_post_meta($source_post_id, 'foto_url', true);

// Načtení layoutu z nastavení
$all_section_keys = function_exists('pehobr_get_home_layout_sections') ? array_keys(pehobr_get_home_layout_sections()) : [];
$layout_order = get_option('pehobr_home_layout_order', $all_section_keys);
$visibility = get_option('pehobr_home_section_visibility', array_fill_keys($all_section_keys, 'on'));
$default_pope_display = get_option('pehobr_pope_section_display', 'graficke');

$sections_html = [];

// Sekce 1: Papežové
ob_start();
$pope_nav_style = get_option('pehobr_pope_nav_style', 'svetle');
?>
<div class="pope-section-container style-<?php echo esc_attr($pope_nav_style); ?>" data-default-view="<?php echo esc_attr($default_pope_display); ?>">
    <div class="pope-items-wrapper view-graficke">
        <?php for ($i = 0; $i < 3; $i++): $item = $grid_items[$i]; $content_html = isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : ''; $has_content = !empty($content_html); $link_url = $has_content ? '#' : home_url('/' . $item['slug'] . '/'); ?>
            <div class="pope-item">
                <a href="<?php echo esc_url($link_url); ?>" class="pope-icon-link" <?php if ($has_content): ?>data-target-id="quote-content-<?php echo esc_attr($item['citat_key']); ?>" data-type="<?php echo esc_attr($item['type']); ?>" data-author-name="<?php echo esc_attr($item['name']); ?>"<?php endif; ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                </a>
                <span class="grid-item-label"><?php echo esc_html($item['label']); ?></span>
            </div>
        <?php endfor; ?>
    </div>
    <div class="pope-text-wrapper view-textove" style="display: none;">
        <?php for ($i = 0; $i < 3; $i++): $item = $grid_items[$i]; $content_html = isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : ''; if (!empty($content_html)): ?>
            <div class="pope-text-item">
                <h3 class="pope-text-author"><?php echo esc_html($item['name']); ?></h3>
                <div class="pope-text-quote"><?php echo wpautop(esc_html($content_html)); ?></div>
            </div>
        <?php endif; endfor; ?>
    </div>
</div>
<?php
$sections_html['pope_section'] = ob_get_clean();


// Sekce 2: Svatí
ob_start();
$saints_nav_style = get_option('pehobr_saints_nav_style', 'svetle');
?>
<div class="saints-section-container style-<?php echo esc_attr($saints_nav_style); ?>">
<div class="saints-items-wrapper">
        <div class="saints-item-boxed">
            <?php
            $augustin_item = $grid_items[3];
            $augustin_has_content = !empty($augustin_photo_url);
            if ($augustin_has_content) :
                ?>
                <a href="<?php echo esc_url($augustin_photo_url); ?>" class="pope-icon-link" data-lightbox="augustin-image" data-title="<?php echo esc_attr($augustin_item['name']); ?>">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $augustin_item['icon']); ?>" alt="<?php echo esc_attr($augustin_item['name']); ?>">
                </a>
            <?php else: ?>
                <span class="pope-icon-link no-link" style="cursor: default;">
                     <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $augustin_item['icon']); ?>" alt="<?php echo esc_attr($augustin_item['name']); ?>">
                </span>
            <?php endif; ?>
            <span class="grid-item-label">sv. Augustin</span>
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
<?php
$sections_html['saints_section'] = ob_get_clean();


// Sekce 3: Akce
ob_start();
$actions_nav_style = get_option('pehobr_actions_nav_style', 'svetle');
?>
<div class="third-row-section-container style-<?php echo esc_attr($actions_nav_style); ?>">
    <div class="third-row-items-wrapper">
        <?php
        for ($i = 5; $i < count($grid_items); $i++):
            $item = $grid_items[$i];
            $icon_to_use = ($actions_nav_style === 'fialove' && isset($item['light_icon'])) ? $item['light_icon'] : $item['icon'];

            // === ZAČÁTEK KLÍČOVÉ ÚPRAVY ===
            if ($item['name'] === 'Modlitba') {
                // Pro "Modlitbu" vždy použijeme přímý odkaz a zrušíme atributy pro popup
                $link_url = home_url('/denni-modlitba/');
                $data_attributes = '';
            } else {
                // Původní logika pro ostatní položky (Bible, Inspirace)
                $content_html = isset($item['citat_key']) && isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
                $has_content = !empty($content_html) || $item['type'] === 'ai_inspiration';
                $link_url = $has_content ? '#' : home_url('/' . $item['slug'] . '/');
                $data_attributes = $has_content ? 'data-target-id="quote-content-' . esc_attr($item['citat_key']) . '" data-type="' . esc_attr($item['type']) . '" data-author-name="' . esc_attr($item['name']) . '"' : '';
            }
            // === KONEC KLÍČOVÉ ÚPRAVY ===
        ?>
            <div class="grid-item-wrapper">
                <a href="<?php echo esc_url($link_url); ?>" class="icon-grid-item" <?php echo $data_attributes; ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $icon_to_use); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                </a>
            </div>
        <?php endfor; ?>
    </div>
</div>
<?php
$sections_html['actions_section'] = ob_get_clean();

// Sekce 4: Navigace pro PC
ob_start();
$desktop_nav_style = get_option('pehobr_desktop_nav_style', 'svetle');
?>
<div id="desktop-nav-grid-container" class="style-<?php echo esc_attr($desktop_nav_style); ?>">
    <div class="desktop-nav-items-wrapper">
        <?php foreach ($desktop_nav_items as $item) : ?>
            <div class="desktop-nav-item">
                <a href="<?php echo esc_url(home_url($item['url'])); ?>" class="desktop-nav-icon-link" aria-label="<?php echo esc_attr($item['name']); ?>">
                    <i class="fa <?php echo esc_attr($item['fa_icon']); ?>" aria-hidden="true"></i>
                </a>
                <span class="grid-item-label"><?php echo esc_html($item['name']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
$sections_html['desktop_nav_section'] = ob_get_clean();

// Sekce 5: Knihovny - ZMĚNA ZDE
ob_start();
$library_nav_style = get_option('pehobr_home_theme_library_section', 'fialove'); // Výchozí hodnota
?>
<div id="library-section-wrapper" class="style-<?php echo esc_attr($library_nav_style); ?>">
    <?php foreach ($library_items as $item) : ?>
        <a href="<?php echo esc_url(home_url($item['url'])); ?>" class="library-item-box">
             <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>"
                 alt="<?php echo esc_attr($item['name']); ?>"
                 data-dark-icon="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>"
                 data-light-icon="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['light_icon']); ?>">
        </a>
    <?php endforeach; ?>
</div>
<?php
$sections_html['library_section'] = ob_get_clean();
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

            <?php
            // Vykreslení sekcí podle uloženého pořadí a viditelnosti
            foreach ($layout_order as $section_slug) {
                if (isset($sections_html[$section_slug]) && isset($visibility[$section_slug]) && $visibility[$section_slug] === 'on') {
                    echo $sections_html[$section_slug];
                }
            }
            ?>

        </div>
    </main>
</div>
<?php get_sidebar(); ?>

<div id="hidden-quotes-container" style="display: none; visibility: hidden;">
    <?php
    foreach ($grid_items as $item) :
        if (isset($item['citat_key'])) {
            if ($item['type'] === 'ai_inspiration') {
                continue;
            }
            $content_html = isset($quotes[$item['citat_key']]) ? $quotes[$item['citat_key']] : '';
            if (!empty($content_html)) :
                $modal_content = '';
                if ($item['type'] === 'video') {
                     $modal_content = $content_html;
                } else {
                     $modal_content = wpautop($content_html);
                     if (isset($item['audio_key'])) {
                         $audio_url = get_post_meta($source_post_id, $item['audio_key'], true);
                         if (!empty($audio_url)) {
                              $modal_content .= '<div class="modal-audio-player" data-audio-src="' . esc_url($audio_url) . '">...</div>';
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

<div id="donation-popup-overlay"></div>
<a href="<?php echo esc_url( home_url('/prosba/') ); ?>" id="donation-popup-container">
    <span>Děkujeme<br>za Vaši podporu</span>
    <span id="donation-timer">7s</span> 
</a>
<?php get_footer(); ?>