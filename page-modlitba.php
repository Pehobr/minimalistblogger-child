<?php
/**
 * Template Name: Stránka pro denní modlitbu
 * Popis: Zobrazuje text zamyšlení a audio přehrávač pro denní modlitbu z denních kapek.
 * VERZE 5: Implementace vlastního audio přehrávače.
 */

get_header(); 
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main modlitba-container" role="main">

        <?php
        $start_date_str = get_option('start_date_setting');
        $post_to_display = null;
        $admin_info = '';

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
                // Zde můžeme v budoucnu logovat chybu
            }
        }
        
        if ($post_to_display) {
            setup_postdata($post_to_display);

            $modlitba_text = get_field('modlitba_text', $post_to_display->ID);
            $modlitba_url = get_field('modlitba_url', $post_to_display->ID);

            ?>
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
                        <div id="modlitba-player-container">
                            <audio id="modlitba-audio-element" src="<?php echo esc_url($modlitba_url); ?>" preload="metadata"></audio>
                            <div class="player-controls">
                                <button id="modlitba-play-pause-btn" class="player-control-btn main-play-btn" aria-label="Přehrát / Pauza">
                                    <i class="fa fa-play" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="progress-wrapper">
                                <div id="modlitba-progress-bar" class="progress-bar-container">
                                    <div id="modlitba-progress" class="progress-bar-progress"></div>
                                </div>
                                <div class="time-display">
                                    <span id="modlitba-current-time">0:00</span> / <span id="modlitba-duration">0:00</span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Pro dnešní den není k dispozici žádná audio nahrávka.</p>
                    <?php endif; ?>
                </div>
            </article>
            <?php
            wp_reset_postdata();

        } else {
            ?>
            <div class="no-content-found">
                <h2>Obsah nenalezen</h2>
                <p>Omlouváme se, ale pro dnešní den se nepodařilo nalézt žádné zamyšlení. Zkuste to prosím později.</p>
            </div>
            <?php
        }
        ?>

    </main>
</div>

<?php
get_sidebar(); // <-- PŘIDANÝ ŘÁDEK
get_footer();
?>