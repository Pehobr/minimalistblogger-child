<?php
/**
 * Template Name: Stránka pro denní modlitbu
 * Popis: Zobrazuje text zamyšlení a audio přehrávač pro denní modlitbu z denních kapek.
 * VERZE 7: Nahrazení čísla názvem dne v archivu.
 */

get_header(); 
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php
        // --- ZOBRAZENÍ AKTUÁLNÍHO DNE ---
        $start_date_str = get_option('start_date_setting');
        $post_to_display = null;
        $day_index = 0;

        if ($start_date_str) {
            try {
                $start_date = new DateTime($start_date_str);
                $today = new DateTime(date('Y-m-d', current_time('timestamp')));
                
                if ($today >= $start_date) {
                    $interval = $start_date->diff($today);
                    $day_index = $interval->days; 

                    $args = array(
                        'post_type'      => 'denni_kapka',
                        'post_status'    => 'publish',
                        'posts_per_page' => 1,
                        'offset'         => $day_index,
                        'orderby'        => 'date',
                        'order'          => 'ASC',
                    );

                    $denni_kapka_query = new WP_Query($args);

                    if ($denni_kapka_query->have_posts()) {
                        $post_to_display = $denni_kapka_query->posts[0];
                    }
                }
            } catch (Exception $e) {
                // Logování chyby
            }
        }
        
        if ($post_to_display) {
            setup_postdata($post_to_display);
            $modlitba_text = get_post_meta($post_to_display->ID, 'modlitba_text', true);
            $modlitba_url = get_post_meta($post_to_display->ID, 'modlitba_url', true);
            ?>
            <div class="modlitba-container">
                <article id="post-<?php echo $post_to_display->ID; ?>" <?php post_class('', $post_to_display->ID); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title">Zamyšlení a modlitba</h1>
                    </header>
                    <div class="entry-content">
                        <?php if ($modlitba_text) : ?>
                            <div class="modlitba-text-content">
                                <?php echo wpautop($modlitba_text); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($modlitba_url) : ?>
                            <div class="modlitba-player-container" data-is-main-player="true">
                                <audio class="modlitba-audio-element" src="<?php echo esc_url($modlitba_url); ?>" preload="metadata"></audio>
                                <div class="player-controls">
                                    <button class="player-control-btn main-play-btn modlitba-play-pause-btn" aria-label="Přehrát / Pauza">
                                        <i class="fa fa-play" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="progress-wrapper">
                                    <div class="progress-bar-container modlitba-progress-bar">
                                        <div class="progress-bar-progress modlitba-progress"></div>
                                    </div>
                                    <div class="time-display">
                                        <span class="modlitba-current-time">0:00</span> / <span class="modlitba-duration">0:00</span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p>Pro dnešní den není k dispozici žádná audio nahrávka.</p>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
            <?php
            wp_reset_postdata();
        } else {
            ?>
            <div class="no-content-found">
                <h2>Obsah nenalezen</h2>
                <p>Omlouváme se, ale pro dnešní den se nepodařilo nalézt žádné zamyšlení.</p>
            </div>
            <?php
        }
        ?>

        <?php // --- TLAČÍTKO A KONTEJNER PRO ARCHIV --- ?>
        <?php if ($day_index > 0): ?>
            <button id="modlitba-archive-toggle" class="modlitba-accordion-btn">Další modlitby</button>
            <div id="past-modlitby-container" style="display: none;">
                <?php
                $args_past = array(
                    'post_type'      => 'denni_kapka',
                    'post_status'    => 'publish',
                    'posts_per_page' => $day_index,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );
                $past_kapka_query = new WP_Query($args_past);

                if ($past_kapka_query->have_posts()) :
                    while ($past_kapka_query->have_posts()) : $past_kapka_query->the_post();
                        $past_post_id = get_the_ID();
                        $past_modlitba_text = get_post_meta($past_post_id, 'modlitba_text', true);
                        $past_modlitba_url = get_post_meta($past_post_id, 'modlitba_url', true);
                        
                        // --- ZMĚNA ZDE: Načtení názvu dne ---
                        $nazev_dne_archiv = get_post_meta($past_post_id, 'nazev_dne', true);
                        if (empty($nazev_dne_archiv)) {
                            $nazev_dne_archiv = get_the_title(); // Záloha, pokud by pole nebylo vyplněno
                        }
                        ?>
                        <hr class="modlitba-divider">
                        <div class="modlitba-container past-modlitba-item">
                            <article id="post-<?php echo $past_post_id; ?>" <?php post_class('', $past_post_id); ?>>
                                <header class="entry-header">
                                    <h2 class="entry-title"><?php echo esc_html($nazev_dne_archiv); ?></h2>
                                </header>
                                <div class="entry-content">
                                    <?php if ($past_modlitba_text) : ?>
                                        <div class="modlitba-text-content">
                                            <?php echo wpautop($past_modlitba_text); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($past_modlitba_url) : ?>
                                        <div class="modlitba-player-container">
                                            <audio class="modlitba-audio-element" src="<?php echo esc_url($past_modlitba_url); ?>" preload="metadata"></audio>
                                            <div class="player-controls">
                                                <button class="player-control-btn main-play-btn modlitba-play-pause-btn" aria-label="Přehrát / Pauza">
                                                    <i class="fa fa-play" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div class="progress-wrapper">
                                                <div class="progress-bar-container modlitba-progress-bar">
                                                    <div class="progress-bar-progress modlitba-progress"></div>
                                                </div>
                                                <div class="time-display">
                                                    <span class="modlitba-current-time">0:00</span> / <span class="modlitba-duration">0:00</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        <?php endif; ?>
        
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>