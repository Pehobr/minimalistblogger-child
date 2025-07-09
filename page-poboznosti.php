<?php
/**
 * Template Name: Aplikace Pobožnosti
 *
 * Popis: Zobrazí denní příspěvek z rubriky "Pobožnosti" podle
 * počtu dní od pevně daného startovního data.
 *
 * @package minimalistblogger-child
 */

get_header();

// Připravíme si proměnnou, do které uložíme data pro JavaScript
$audio_data_pro_js = array();
?>

<div id="primary" class="featured-content content-area poboznosti-app">
    <main id="main" class="site-main">

        <?php
        $kategorie_slug = 'poboznosti';
        $startovni_datum_str = get_option('start_date_setting', '2026-02-18'); 

        try {
            $startovni_datum = new DateTime($startovni_datum_str, new DateTimeZone('Europe/Prague'));
            $dnesni_datum = new DateTime('today', new DateTimeZone('Europe/Prague'));

            if ($dnesni_datum < $startovni_datum) {
                // Zpráva pokud cyklus ještě nezačal
                echo '<div class="zprava-aplikace"><h2>Pobožnosti ještě nezačaly.</h2><p>Cyklus začíná dne ' . esc_html($startovni_datum->format('d. m. Y')) . '. Prosím, vraťte se později.</p></div>';
            } else {
                $interval = $startovni_datum->diff($dnesni_datum);
                $offset_prispevku = $interval->days;

                $args = array(
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'category_name'  => $kategorie_slug,
                    'posts_per_page' => 1,
                    'offset'         => $offset_prispevku,
                    'orderby'        => 'date',
                    'order'          => 'ASC',
                );

                $denni_poboznost_query = new WP_Query($args);

                if ( $denni_poboznost_query->have_posts() ) {
                    while ( $denni_poboznost_query->have_posts() ) {
                        $denni_poboznost_query->the_post();
                        global $post;

                        // Zjistíme, jestli existuje alespoň jedno audio pole
                        $all_meta = get_post_meta($post->ID);
                        $has_audio = false;
                        foreach ($all_meta as $key => $value) {
                            if (strpos($key, 'audio_') === 0 && !empty($value[0])) {
                                $has_audio = true;
                                break;
                            }
                        }

                        // Pokud ano, zobrazíme hlavní přehrávač
                        if ($has_audio) {
                            echo '
                            <div id="poboznosti-player-container">
                                <div class="player-controls">
                                    <button id="poboznosti-prev-btn" class="player-control-btn" aria-label="Předchozí stopa"><i class="fa fa-step-backward" aria-hidden="true"></i></button>
                                    <button id="poboznosti-play-pause-btn" class="player-control-btn main-play-btn" aria-label="Přehrát / Pauza"><i class="fa fa-play" aria-hidden="true"></i></button>
                                    <button id="poboznosti-next-btn" class="player-control-btn" aria-label="Další stopa"><i class="fa fa-step-forward" aria-hidden="true"></i></button>
                                </div>
                                <div class="progress-wrapper">
                                    <div id="poboznosti-progress-bar" class="progress-bar-container"><div id="poboznosti-progress" class="progress-bar-progress"></div></div>
                                    <div id="poboznosti-track-title" class="track-title"></div>
                                </div>
                            </div>';
                        }
                        
                        // Zobrazení obsahu příspěvku
                        get_template_part( 'template-parts/content', 'single' );

                        // Sběr audio dat pro JavaScript v přesném pořadí
                        $audio_fields_ordered = [
                            'audio_1sloka', 'audio_uvodnimodlitba', 'audio_1cteni', 'audio_zalm', 'audio_2cteni', 
                            'audio_2sloka', 'audio_evang', 'audio_3sloka', 'audio_impulz', 'audio_4sloka', 
                            'audio_zaverecnamodlitba', 'audio_5sloka'
                        ];

                        $temp_audio_data = [];
                        foreach ($audio_fields_ordered as $meta_key) {
                            $url = get_post_meta($post->ID, $meta_key, true);
                            if (!empty($url)) {
                                $temp_audio_data[$meta_key] = esc_url($url);
                            }
                        }
                        $audio_data_pro_js = $temp_audio_data;
                    }
                } else {
                    echo '<div class="zprava-aplikace"><h2>Pro dnešní den není k dispozici žádná pobožnost.</h2></div>';
                }
                wp_reset_postdata();
            }
        } catch (Exception $e) {
            echo '<div class="zprava-aplikace chyba"><h2>Chyba v nastavení šablony Pobožnosti.</h2></div>';
        }
        ?>
    </main>
</div>

<?php 
// Blok, který předá data do JavaScriptu. Vloží se pouze pokud byla nalezena nějaká audio data.
if ( ! empty( $audio_data_pro_js ) ) : ?>
<script type="text/javascript" id="poboznosti-data-js">
    var poboznostiUdaje = {
        "audioUrls": <?php echo wp_json_encode( $audio_data_pro_js ); ?>
    };
</script>
<?php endif;

get_sidebar(); // <-- PŘIDANÝ ŘÁDEK
get_footer();
?>