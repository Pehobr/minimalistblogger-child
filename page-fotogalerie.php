<?php
/**
 * Template Name: Fotogalerie sv. Augustina
 * Description: Zobrazí fotogalerii obrázků sv. Augustina, které se postupně odhalují každý den.
 *
 * @package minimalistblogger-child
 */

get_header();

// Nastavení startovního data (stejné jako v ostatních částech aplikace)
$start_date_str = get_option('start_date_setting', '2026-02-18');
$image_urls = [];

try {
    $start_date = new DateTime($start_date_str, new DateTimeZone('Europe/Prague'));
    $today = new DateTime('today', new DateTimeZone('Europe/Prague'));

    // Pokud ještě nezačal cyklus, nezobrazíme nic
    if ($today >= $start_date) {
        $interval = $start_date->diff($today);
        $days_to_show = $interval->days + 1; // Počet dní včetně dnešního

        // Načteme všechny příspěvky od začátku až po dnešek
        $args = [
            'post_type'      => 'denni_kapka',
            'post_status'    => 'publish',
            'posts_per_page' => $days_to_show,
            'orderby'        => 'date',
            'order'          => 'ASC',
            'meta_query'     => [
                [
                    'key'     => 'citat_augustin', // Klíč pole s URL obrázku
                    'value'   => '',
                    'compare' => '!=',
                ],
            ],
        ];
        $daily_query = new WP_Query($args);

        if ($daily_query->have_posts()) {
            while ($daily_query->have_posts()) {
                $daily_query->the_post();
                $image_url = get_post_meta(get_the_ID(), 'citat_augustin', true);
                if (filter_var($image_url, FILTER_VALIDATE_URL)) {
                    $image_urls[] = $image_url;
                }
            }
            wp_reset_postdata();
        }
    }
} catch (Exception $e) {
    // Chybu můžeme zalogovat, ale na frontendu ji teď nezobrazujeme
}
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Fotogalerie sv. Augustina</h1>
            </header>
            <div class="entry-content">
                <?php if (!empty($image_urls)) : ?>
                    <div class="augustin-gallery">
                        <?php foreach ($image_urls as $url) : ?>
                            <a href="<?php echo esc_url($url); ?>" data-lightbox="augustin-gallery" class="gallery-item">
                                <img src="<?php echo esc_url($url); ?>" alt="Obrázek sv. Augustina" loading="lazy">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p>Galerie je zatím prázdná. První obrázek se zobrazí v den zahájení.</p>
                <?php endif; ?>
            </div>
        </article>
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>