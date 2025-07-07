<?php
/**
 * Template Name: Video Kapky App
 * Description: Zobrazí přehled video a audio playlistů dynamicky načtených z nastavení a rozdělených do kategorií.
 * VERZE 8: Přidána ikona audia do přehrávače.
 *
 * @package minimalistblogger-child
 */

get_header();

// --- Načtení dynamických dat z nastavení ---
$playlists = get_option('pehobr_youtube_playlists', array());

// Klíč pro YouTube API
$api_key = 'AIzaSyBOfR8mbqwVZ-MueLr0BQzePKdzbOPuC_8';

// Seskupení playlistů podle kategorie
$grouped_playlists = [];
foreach ($playlists as $playlist) {
    $category = !empty($playlist['category']) ? trim($playlist['category']) : 'Ostatní';
    if (!isset($grouped_playlists[$category])) {
        $grouped_playlists[$category] = [];
    }
    $grouped_playlists[$category][] = $playlist;
}

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Video a Audio Kapky</h1>
            </header>
            <div class="entry-content">

                <?php if ( ! empty( $grouped_playlists ) ) : ?>

                    <?php foreach ($grouped_playlists as $category_name => $category_playlists) : ?>
                        
                        <h2 class="video-section-title"><?php echo esc_html($category_name); ?></h2>

                        <div class="media-grid-container" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
                            <?php foreach ( $category_playlists as $index => $item ) : ?>
                                <?php
                                if ( empty($item['playlist_id']) ) {
                                    continue;
                                }
                                $image_url = !empty($item['image_url']) ? $item['image_url'] : get_stylesheet_directory_uri() . '/img/default-youtube-placeholder.jpg';
                                $title = !empty($item['title']) ? $item['title'] : 'Přehrát video';
                                $unique_index = esc_attr($category_name) . '-' . $index;
                                $embed_url = 'https://www.youtube.com/embed/videoseries?list=' . esc_attr($item['playlist_id']);
                                ?>
                                <div class="media-item-block">
                                    <div class="video-grid-item" data-embed-url="<?php echo $embed_url; ?>">
                                        <div class="video-item-image" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
                                        <h3 class="video-item-title"><?php echo esc_html($title); ?></h3>
                                    </div>
                                    <div id="audio-player-<?php echo $unique_index; ?>" class="custom-audio-player loading" data-playlist-id="<?php echo esc_attr($item['playlist_id']); ?>" data-api-key="<?php echo esc_attr($api_key); ?>">
                                        <div class="player-ui">
                                            <div class="player-info">
                                                <h3 class="player-video-title">Načítání audia...</h3>
                                                
                                                <div class="player-controls">
                                                    <div class="player-controls-left">
                                                        <button class="player-play-pause-btn" aria-label="Přehrát / Pauza"><i class="fa fa-play"></i></button>
                                                        <div class="player-status">Připojování...</div>
                                                    </div>
                                                    <div class="player-audio-icon">
                                                        <i class="fa fa-headphones" aria-hidden="true"></i>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div id="youtube-player-container-<?php echo $unique_index; ?>" class="youtube-player-container"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php endforeach; ?>

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