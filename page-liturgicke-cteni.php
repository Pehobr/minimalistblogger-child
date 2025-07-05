<?php
/**
 * Template Name: Aplikace Liturgické Čtení
 *
 * Popis: Zobrazí denní příspěvek z rubriky "Liturgické čtení" podle
 * počtu dní od pevně daného startovního data.
 *
 * @package minimalistblogger-child
 */

get_header(); // Načte hlavičku šablony

// Připravíme si proměnnou, do které uložíme data pro JavaScript
$audio_data_pro_js = array();
?>

<div id="primary" class="featured-content content-area liturgicke-cteni-app">
    <main id="main" class="site-main">

        <?php
        // ====================================================================
        // NASTAVENÍ APLIKACE
        // ====================================================================
        $kategorie_slug = 'liturgicke-cteni';
        
        // ZMĚNA: Datum se nyní načítá z globálního nastavení WordPressu
        $startovni_datum_str = get_option('start_date_setting', '2026-02-18');
        // ====================================================================

        try {
            $startovni_datum = new DateTime($startovni_datum_str, new DateTimeZone('Europe/Prague'));
            $dnesni_datum = new DateTime('today', new DateTimeZone('Europe/Prague'));

            if ($dnesni_datum < $startovni_datum) {
                echo '<div class="zprava-aplikace">';
                echo '<h2>Liturgická čtení ještě nezačala.</h2>';
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

                $denni_cteni_query = new WP_Query($args);

                if ( $denni_cteni_query->have_posts() ) {
                    while ( $denni_cteni_query->have_posts() ) {
                        $denni_cteni_query->the_post();
                        
                        global $post;
                        $cteni1_url_check = get_post_meta($post->ID, 'audio_cteni_1', true);
                        $cteni2_url_check = get_post_meta($post->ID, 'audio_cteni_2', true);
                        $evangelium_url_check = get_post_meta($post->ID, 'audio_evangelium', true);

                        if (!empty($cteni1_url_check) || !empty($cteni2_url_check) || !empty($evangelium_url_check)) {
                            echo '
                            <div id="playlist-player-container">
                                <div class="player-controls">
                                    <button id="playlist-prev-btn" class="player-control-btn" aria-label="Předchozí stopa">
                                        <i class="fa fa-step-backward" aria-hidden="true"></i>
                                    </button>
                                    <button id="playlist-play-pause-btn" class="player-control-btn main-play-btn" aria-label="Přehrát / Pauza">
                                        <i class="fa fa-play" aria-hidden="true"></i>
                                    </button>
                                    <button id="playlist-next-btn" class="player-control-btn" aria-label="Další stopa">
                                        <i class="fa fa-step-forward" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="progress-wrapper">
                                    <div id="playlist-progress-bar" class="progress-bar-container">
                                        <div id="playlist-progress" class="progress-bar-progress"></div>
                                    </div>
                                    <div id="playlist-track-title" class="track-title"></div>
                                </div>
                            </div>';
                        }

                        // ZOBRAZENÍ OBSAHU PŘÍSPĚVKU
                        get_template_part( 'template-parts/content', 'single' );

                        $audio_data = array();
                        $cteni1_url = get_post_meta($post->ID, 'audio_cteni_1', true);
                        $cteni2_url = get_post_meta($post->ID, 'audio_cteni_2', true);
                        $evangelium_url = get_post_meta($post->ID, 'audio_evangelium', true);

                        if (!empty($cteni1_url)) $audio_data['cteni_1'] = esc_url($cteni1_url);
                        if (!empty($cteni2_url)) $audio_data['cteni_2'] = esc_url($cteni2_url);
                        if (!empty($evangelium_url)) $audio_data['evangelium'] = esc_url($evangelium_url);
                        
                        $audio_data_pro_js = $audio_data;
                    }
                } else {
                    echo '<div class="zprava-aplikace">';
                    echo '<h2>Pro dnešní den není k dispozici žádné čtení.</h2>';
                    echo '<p>Je možné, že cyklus již skončil, nebo pro tento den ještě nebylo přidáno čtení.</p>';
                    echo '</div>';
                }
                wp_reset_postdata();
            }
        } catch (Exception $e) {
            echo '<div class="zprava-aplikace chyba">';
            echo '<h2>Chyba v nastavení</h2>';
            echo '<p>Bylo zadáno neplatné startovní datum. Prosím, zkontrolujte kód v souboru šablony.</p>';
            echo '</div>';
        }
        ?>

    </main></div><?php
// Blok pro vložení dat do JavaScriptu
if ( ! empty( $audio_data_pro_js ) ) :
?>
<script type="text/javascript" id="liturgicke-cteni-data-js">
    var liturgickeUdaje = {
        "audioUrls": <?php echo wp_json_encode( $audio_data_pro_js ); ?>
    };
</script>
<?php
endif;

get_footer();
?>