<?php
/**
 * Template Name: Playlist Audio App
 * Description: Zobrazí audio přehrávač pro poslední video z YouTube playlistu.
 *
 * @package minimalistblogger-child
 */

get_header();

// ZMĚNA: Načítáme ID playlistu i API klíč z vlastních polí stránky.
$playlist_id = get_post_meta(get_the_ID(), 'youtube_playlist_id', true);
$api_key = get_post_meta(get_the_ID(), 'youtube_api_key', true);

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>
            <div class="entry-content">
                
                <?php if ( ! empty( $playlist_id ) && ! empty( $api_key ) ) : ?>
                    <div id="custom-audio-player" class="loading" 
                         data-playlist-id="<?php echo esc_attr($playlist_id); ?>" 
                         data-api-key="<?php echo esc_attr($api_key); ?>">
                        
                        <div class="player-ui">
                            <button id="player-play-pause-btn" aria-label="Přehrát / Pauza">
                                <i class="fa fa-play"></i>
                            </button>
                            <div class="player-info">
                                <h2 id="player-title">Načítání nejnovější epizody...</h2>
                                <div id="player-status">Připojování k YouTube...</div>
                            </div>
                        </div>
                        
                        <div id="youtube-player-container"></div>
                    </div>
                <?php else: ?>
                    <p>Chybí ID playlistu nebo API klíč. Doplňte je prosím do vlastních polí této stránky (pole 'youtube_playlist_id' a 'youtube_api_key').</p>
                <?php endif; ?>

                 <?php
                // Zobrazí obsah napsaný v editoru stránky
                while ( have_posts() ) :
                    the_post();
                    the_content();
                endwhile;
                ?>

            </div>
        </article>
    </main>
</div>

<?php
get_footer();
?>