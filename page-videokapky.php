<?php
/**
 * Template Name: Video Kapky App
 * Description: Zobrazí přehled video a audio playlistů v oddělených sekcích.
 *
 * @package minimalistblogger-child
 */

get_header();

// --- DATA PRO SEKCI "DENNÍ PROMLUVY" ---
$daily_items = [
    [
        'title' => 'Televize NOE',
        'image_url' => 'https://zpravy.proglas.cz/res/archive/280/062778.png?seek=1498474045',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&list=PLQ0VblkXIA4wokyX7NZm7MvBdTRsWR8X6&',
        'youtube_playlist_id' => 'PLQ0VblkXIA4wokyX7NZm7MvBdTRsWR8X6',
    ],
    [
        'title' => 'P. Šebestián, OFM',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2023/01/maxresdefault-300x169.jpg',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&list=UUQXkJsp9wBiSzNQ-JbEqMWw&',
        'youtube_playlist_id' => 'UUQXkJsp9wBiSzNQ-JbEqMWw',
    ],
];

// --- DATA PRO SEKCI "PŘÍPRAVA NA NEDĚLI" ---
$weekly_items = [
     [
        'title' => 'Lomecká vigilie',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2022/02/Zachyceni_webu_5-2-2022_153124_www.lomec_.cz_-300x183.jpg',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&list=PLmTG1ecR3a_QRajXuNldZweNx1UQtIJSJ&',
        'youtube_playlist_id' => 'PLmTG1ecR3a_QRajXuNldZweNx1UQtIJSJ',
    ],
    [
        'title' => 'Dýchej Slovo',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2024/01/02301185-300x169.jpeg',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&list=PLP2LVEgwOzCzHllFx8_SGMN343FE71LXe',
        'youtube_playlist_id' => 'PLP2LVEgwOzCzHllFx8_SGMN343FE71LXe',
    ],
    [
        'title' => 'P. Tomáš Halík',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2021/02/TomasHalik-300x160.png',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&list=PLAtQGAGIuIYyYTvKYbI7YSu8QtIZ5xHFo&',
        'youtube_playlist_id' => 'PLAtQGAGIuIYyYTvKYbI7YSu8QtIZ5xHFo',
    ],
];

$api_key = 'AIzaSyBOfR8mbqwVZ-MueLr0BQzePKdzbOPuC_8';

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Video a Audio Kapky</h1>
            </header>
            <div class="entry-content">

                <h2 class="video-section-title">Denní promluvy</h2>
                <div class="media-grid-container daily-grid">
                    <?php foreach ($daily_items as $index => $item) : ?>
                        <div class="media-item-block">
                            <div class="video-grid-item" data-embed-url="<?php echo esc_attr($item['youtube_embed_url']); ?>">
                                <div class="video-item-image" style="background-image: url('<?php echo esc_url($item['image_url']); ?>');"></div>
                                <h3 class="video-item-title"><?php echo esc_html($item['title']); ?></h3>
                            </div>
                            <div id="audio-player-<?php echo $index; ?>" class="custom-audio-player loading" data-playlist-id="<?php echo esc_attr($item['youtube_playlist_id']); ?>" data-api-key="<?php echo esc_attr($api_key); ?>">
                                <div class="player-ui"><div class="player-info"><h3 class="player-video-title">Načítání audia...</h3><div class="player-controls"><button class="player-play-pause-btn" aria-label="Přehrát / Pauza"><i class="fa fa-play"></i></button><div class="player-status">Připojování...</div></div></div></div>
                                <div id="youtube-player-container-<?php echo $index; ?>" class="youtube-player-container"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h2 class="video-section-title">Příprava na neděli</h2>
                <div class="media-grid-container weekly-grid">
                     <?php foreach ($weekly_items as $index => $item) : $audio_index = $index + count($daily_items); // Zajistíme unikátní ID pro audio přehrávače ?>
                        <div class="media-item-block">
                             <div class="video-grid-item" data-embed-url="<?php echo esc_attr($item['youtube_embed_url']); ?>">
                                <div class="video-item-image" style="background-image: url('<?php echo esc_url($item['image_url']); ?>');"></div>
                                <h3 class="video-item-title"><?php echo esc_html($item['title']); ?></h3>
                            </div>
                             <div id="audio-player-<?php echo $audio_index; ?>" class="custom-audio-player loading" data-playlist-id="<?php echo esc_attr($item['youtube_playlist_id']); ?>" data-api-key="<?php echo esc_attr($api_key); ?>">
                                <div class="player-ui"><div class="player-info"><h3 class="player-video-title">Načítání audia...</h3><div class="player-controls"><button class="player-play-pause-btn" aria-label="Přehrát / Pauza"><i class="fa fa-play"></i></button><div class="player-status">Připojování...</div></div></div></div>
                                <div id="youtube-player-container-<?php echo $audio_index; ?>" class="youtube-player-container"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

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