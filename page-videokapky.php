<?php
/**
 * Template Name: Video Kapky App
 * Description: Zobrazí přehled video playlistů, které se otevírají v modálním okně.
 *
 * @package minimalistblogger-child
 */

get_header();

// SEKCE PRO DENNÍ VIDEA
$daily_videos = [
    [
        'title' => 'Televize NOE',
        'image_url' => 'https://zpravy.proglas.cz/res/archive/280/062778.png?seek=1498474045',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&list=PLQ0VblkXIA4wokyX7NZm7MvBdTRsWR8X6&'
    ],
    [
        'title' => 'P. Šebestián, OFM',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2023/01/maxresdefault-300x169.jpg',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&amp;list=UUQXkJsp9wBiSzNQ-JbEqMWw&amp' // Příklad, nahraďte skutečným playlistem
    ],
];

// SEKCE PRO TÝDENNÍ VIDEA
$weekly_videos = [
    [
        'title' => 'Lomecká vigilie',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2022/02/Zachyceni_webu_5-2-2022_153124_www.lomec_.cz_-300x183.jpg',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&amp;list=PLmTG1ecR3a_QRajXuNldZweNx1UQtIJSJ&amp' // Příklad, nahraďte skutečným playlistem
    ],
    [
        'title' => 'Dýchej Slovo',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2024/01/02301185-300x169.jpeg',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&list=PLP2LVEgwOzCzHllFx8_SGMN343FE71LXe' // Příklad, nahraďte skutečným playlistem
    ],
    [
        'title' => 'P. Tomáš Halík',
        'image_url' => 'https://postnikapky.cz/wp-content/uploads/2021/02/TomasHalik-300x160.png',
        'youtube_embed_url' => 'https://www.youtube.com/embed?listType=playlist&amp;list=PLAtQGAGIuIYyYTvKYbI7YSu8QtIZ5xHFo&amp' // Příklad, nahraďte skutečným playlistem
    ],
];

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Video Kapky</h1>
            </header>
            <div class="entry-content">

                <h2 class="video-section-title">Denní promluvy</h2>
                <div class="video-grid-container daily-videos">
                    <?php foreach ($daily_videos as $category) : ?>
                        <div class="video-grid-item" data-embed-url="<?php echo esc_attr($category['youtube_embed_url']); ?>">
                            <div class="video-item-image" style="background-image: url('<?php echo esc_url($category['image_url']); ?>');"></div>
                            <h3 class="video-item-title"><?php echo esc_html($category['title']); ?></h3>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h2 class="video-section-title">Příprava na neděli</h2>
                <div class="video-grid-container weekly-videos">
                    <?php foreach ($weekly_videos as $category) : ?>
                        <div class="video-grid-item" data-embed-url="<?php echo esc_attr($category['youtube_embed_url']); ?>">
                            <div class="video-item-image" style="background-image: url('<?php echo esc_url($category['image_url']); ?>');"></div>
                            <h3 class="video-item-title"><?php echo esc_html($category['title']); ?></h3>
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
    <div id="video-modal-content">
        </div>
</div>

<?php
get_footer();
?>