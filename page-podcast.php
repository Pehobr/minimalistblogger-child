<?php
/**
 * Template Name: Podcast
 * Description: Zobrazí stránku s podcastem načteným z externího RSS kanálu.
 * VERZE 2.3: Implementován vlastní design přehrávače.
 */
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page page-podcast">
            <header class="entry-header">
                <h1 class="entry-title">Podcast Postní kapky</h1>
            </header>
            <div class="entry-content">
                <div class="podcast-episode-list">
                    <?php
                    include_once(ABSPATH . WPINC . '/feed.php');
                    $rss_url = 'https://anchor.fm/s/e86f5db8/podcast/rss';
                    $feed = fetch_feed($rss_url);

                    if (!is_wp_error($feed)) :
                        $max_items = $feed->get_item_quantity(7);
                        $rss_items = $feed->get_items(0, $max_items);
                    else :
                        $rss_items = [];
                    endif;

                    if (empty($rss_items)) : ?>
                        <div class="podcast-episode">
                            <p>Podcast se nepodařilo načíst nebo zatím neobsahuje žádné epizody.</p>
                        </div>
                    <?php else :
                        foreach ($rss_items as $item) :
                            $audio_url = $item->get_enclosure()->get_link();
                            ?>
                            <div class="podcast-episode">
                                <h2 class="episode-title"><?php echo esc_html($item->get_title()); ?></h2>
                                
                                <?php if ($audio_url) : ?>
                                    <div class="podcast-player" data-audio-src="<?php echo esc_url($audio_url); ?>">
                                        <div class="pp-top-row">
                                            <button class="pp-play-pause-btn" aria-label="Přehrát / Pauza">
                                                <i class="fa fa-play" aria-hidden="true"></i>
                                            </button>
                                            <div class="pp-time-display">
                                                <span class="pp-current-time">0:00</span>
                                                <span class="pp-time-divider">/</span>
                                                <span class="pp-duration">0:00</span>
                                            </div>
                                        </div>
                                        <div class="pp-slider-wrapper">
                                            <input type="range" class="pp-seek-slider" value="0" min="0" max="100" step="0.1">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="episode-notes">
                                    <?php echo wp_kses_post($item->get_description()); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    </main>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>