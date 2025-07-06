<?php
/**
 * Template Name: Video Kapky App
 * Description: Zobrazí přehled video a audio playlistů dynamicky načtených z nastavení.
 * VERZE 4: Zobrazuje název playlistu.
 *
 * @package minimalistblogger-child
 */

get_header();

// --- Načtení dynamických dat z nastavení ---
$playlists = get_option('pehobr_youtube_playlists', array());

// Klíč pro YouTube API (do budoucna by bylo lepší ho mít také v nastavení)
$api_key = 'AIzaSyBOfR8mbqwVZ-MueLr0BQzePKdzbOPuC_8';

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Video a Audio Kapky</h1>
            </header>
            <div class="entry-content">

                <?php if ( ! empty( $playlists ) ) : ?>
                    <div class="media-grid-container" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
                        <?php foreach ( $playlists as $index => $item ) : ?>
                            <?php
                            if ( empty($item['playlist_id']) ) {
                                continue;
                            }
                            $image_url = !empty($item['image_url']) ? $item['image_url'] : get_stylesheet_directory_uri() . '/img/default-youtube-placeholder.jpg';
                            $title = !empty($item['title']) ? $item['title'] : 'Přehrát video';
                            ?>
                            <div class="media-item-block">
                                <div class="video-grid-item" data-embed-url="http://googleusercontent.com/youtube.com/embed?listType=playlist&list=<?php echo esc_attr($item['playlist_id']); ?>">
                                    <div class="video-item-image" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
                                    <h3 class="video-item-title"><?php echo esc_html($title); ?></h3>
                                </div>
                                <div id="audio-player-<?php echo $index; ?>" class="custom-audio-player loading" data-playlist-id="<?php echo esc_attr($item['playlist_id']); ?>" data-api-key="<?php echo esc_attr($api_key); ?>">
                                    <div class="player-ui">
                                        <div class="player-info">
                                            <h3 class="player-video-title">Načítání audia...</h3>
                                            <div class="player-controls">
                                                <button class="player-play-pause-btn" aria-label="Přehrát / Pauza"><i class="fa fa-play"></i></button>
                                                <div class="player-status">Připojování...</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="youtube-player-container-<?php echo $index; ?>" class="youtube-player-container"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p style="text-align: center;">Zatím nebyly přidány žádné YouTube playlisty. Můžete je přidat v menu <a href="<?php echo esc_url(admin_url('admin.php?page=pehobr-youtube-settings')); ?>">Postní kapky -> YouTube Playlisty</a>.</p>
                <?php endif; ?>

            </div>
        </article>
    </main>
</div>

<div id="video-modal-overlay" style="display: none;"></div>
<div id="video-modal-container" style="display: none;">
    <button id="video-modal-close-btn">×</button>
    <div id="video-modal-content"></div>
</div>

<?php
get_footer();
?>