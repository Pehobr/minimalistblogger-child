<?php
/**
 * Template Name: Audio YouTube App
 * Description: Zobrazí přehled pěti audio playlistů z YouTube.
 *
 * @package minimalistblogger-child
 */

get_header();

// Pole s daty pro pět audio playlistů
$audio_playlists = [
    [
        'title' => 'TV Noe',
        'image_url' => 'https://zpravy.proglas.cz/res/archive/280/062778.png?seek=1498474045',
        'youtube_playlist_id' => 'PLQ0VblkXIA4wokyX7NZm7MvBdTRsWR8X6', // Příklad ID
    ],
    [
        'title' => 'P. Šebestián, OFM',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2023/01/maxresdefault-300x169.jpg',
        'youtube_playlist_id' => 'UUQXkJsp9wBiSzNQ-JbEqMWw', // Příklad ID
    ],
    [
        'title' => 'Lomecká vigilie',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2022/02/Zachyceni_webu_5-2-2022_153124_www.lomec_.cz_-300x183.jpg',
        'youtube_playlist_id' => 'PLmTG1ecR3a_QRajXuNldZweNx1UQtIJSJ', // Příklad ID
    ],
    [
        'title' => 'Dýchej Slovo',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2024/01/02301185-300x169.jpeg',
        'youtube_playlist_id' => 'PLP2LVEgwOzCzHllFx8_SGMN343FE71LXe', // Příklad ID
    ],
    [
        'title' => 'P. Tomáš Halík',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2021/02/TomasHalik-300x160.png',
        'youtube_playlist_id' => 'PLAtQGAGIuIYyYTvKYbI7YSu8QtIZ5xHFo', // Příklad ID
    ],
];

// DŮLEŽITÉ: Vložte sem váš YouTube API klíč
$api_key = 'AIzaSyBOfR8mbqwVZ-MueLr0BQzePKdzbOPuC_8';

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Audio YouTube</h1>
            </header>
            <div class="entry-content">

                <div class="audio-youtube-container">
                    <?php foreach ($audio_playlists as $index => $playlist) : ?>
                        <div id="audio-player-<?php echo $index; ?>" class="custom-audio-player loading"
                             data-playlist-id="<?php echo esc_attr($playlist['youtube_playlist_id']); ?>"
                             data-api-key="<?php echo esc_attr($api_key); ?>">

                            <div class="player-ui">
                                <div class="player-image" style="background-image: url('<?php echo esc_url($playlist['image_url']); ?>');"></div>
                                <div class="player-info">
                                    <h2 class="player-playlist-title"><?php echo esc_html($playlist['title']); ?></h2>
                                    <h3 class="player-video-title">Načítání...</h3>
                                    <div class="player-controls">
                                        <button class="player-play-pause-btn" aria-label="Přehrát / Pauza">
                                            <i class="fa fa-play"></i>
                                        </button>
                                        <div class="player-status">Připojování...</div>
                                    </div>
                                </div>
                            </div>
                            <div id="youtube-player-container-<?php echo $index; ?>" class="youtube-player-container"></div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </article>
    </main>
</div>

<?php
get_footer();
?>