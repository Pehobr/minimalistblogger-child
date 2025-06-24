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

$audio_data_pro_js = array();
?>

<div id="primary" class="featured-content content-area poboznosti-app">
    <main id="main" class="site-main">

        <?php
        // ====================================================================
        // NASTAVENÍ APLIKACE POBOŽNOSTI
        // ====================================================================
        $kategorie_slug = 'poboznosti'; // Změněno na novou rubriku
        $startovni_datum_str = '2025-06-23'; // <--- ZDE NASTAVTE START DATUM PRO POBOŽNOSTI
        // ====================================================================

        try {
            $startovni_datum = new DateTime($startovni_datum_str, new DateTimeZone('Europe/Prague'));
            $dnesni_datum = new DateTime('today', new DateTimeZone('Europe/Prague'));

            if ($dnesni_datum < $startovni_datum) {
                echo '<div class="zprava-aplikace">';
                echo '<h2>Pobožnosti ještě nezačaly.</h2>';
                echo '<p>Cyklus začíná dne ' . esc_html($startovni_datum->format('d. m. Y')) . '. Prosím, vraťte se později.</p>';
                echo '</div>';
            } else {
                $interval = $startovni_datum->diff($dnesni_datum);
                $pocet_dni_od_startu = $interval->days;
                $offset_prispevku = $pocet_dni_od_startu;

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
                        
                        // Zde je logika pro vložení přehrávače, pokud existuje audio.
                        // Upraveno pro dynamické hledání 'audio_cast_'.
                        $all_meta = get_post_meta($post->ID);
                        $has_audio = false;
                        foreach ($all_meta as $key => $value) {
                            if (strpos($key, 'audio_cast_') === 0 && !empty($value[0])) {
                                $has_audio = true;
                                break;
                            }
                        }

                        if ($has_audio) {
                            echo '
                            <div id="poboznosti-player-container">
                                <div class="player-controls">
                                    <button id="poboznosti-prev-btn" class="player-control-btn" aria-label="Předchozí stopa">
                                        <i class="fa fa-step-backward" aria-hidden="true"></i>
                                    </button>
                                    <button id="poboznosti-play-pause-btn" class="player-control-btn main-play-btn" aria-label="Přehrát / Pauza">
                                        <i class="fa fa-play" aria-hidden="true"></i>
                                    </button>
                                    <button id="poboznosti-next-btn" class="player-control-btn" aria-label="Další stopa">
                                        <i class="fa fa-step-forward" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="progress-wrapper">
                                    <div id="poboznosti-progress-bar" class="progress-bar-container">
                                        <div id="poboznosti-progress" class="progress-bar-progress"></div>
                                    </div>
                                    <div id="poboznosti-track-title" class="track-title"></div>
                                </div>
                            </div>';
                        }
                        
                        // Zobrazení obsahu příspěvku
                        get_template_part( 'template-parts/content', 'single' );

                        // Sběr audio dat pro JavaScript
                        // Nová dynamická metoda
                        $audio_data = [];
                        $i = 1;
                        while(true) {
                            $meta_key = 'audio_cast_' . $i;
                            $url = get_post_meta($post->ID, $meta_key, true);
                            if (!empty($url)) {
                                $audio_data[$meta_key] = esc_url($url);
                                $i++;
                            } else {
                                break;
                            }
                        }
                        $audio_data_pro_js = $audio_data;
                    }
                } else {
                    echo '<div class="zprava-aplikace">';
                    echo '<h2>Pro dnešní den není k dispozici žádná pobožnost.</h2>';
                    echo '</div>';
                }
                wp_reset_postdata();
            }
        } catch (Exception $e) {
            echo '<div class="zprava-aplikace chyba">';
            echo '<h2>Chyba v nastavení šablony Pobožnosti.</h2>';
            echo '</div>';
        }
        ?>

    </main></div><?php

if ( ! empty( $audio_data_pro_js ) ) :
?>
<script type="text/javascript" id="poboznosti-data-js">
    var poboznostiUdaje = {
        "audioUrls": <?php echo wp_json_encode( $audio_data_pro_js ); ?>
    };
</script>
<?php
endif;

get_footer();