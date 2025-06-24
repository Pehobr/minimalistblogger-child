<?php
/**
 * Template Name: Přehrávač rádií
 * Description: Zobrazí stránku s přehrávačem internetových rádií. Data načítá z Custom Fields.
 *
 * @package minimalistblogger-child
 */

get_header();

// Získáme ID aktuální stránky pro načtení Custom Fields
$page_id = get_the_ID();

// <<<=== ZMĚNA ZDE: Přidání 'site_url' k rádiím ===>>>
$radia = [
    [
        'name' => 'Proglas',
        'icon' => 'ikona-proglas.png',
        'meta_key' => 'radio_stream_proglas',
        'site_url' => 'https://www.proglas.cz/'
    ],
    [
        'name' => 'Timea',
        'icon' => 'ikona-timea.png',
        'meta_key' => 'radio_stream_timea',
        'site_url' => 'https://radiotimea.jednoduse.cz/'
    ],
    [
        'name' => 'Lumen',
        'icon' => 'ikona-lumen.png',
        'meta_key' => 'radio_stream_lumen',
        'site_url' => 'https://www.lumen.sk/'
    ]
];
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">

        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Rádia</h1>
            </header>

            <div class="entry-content">
                <div id="radio-player-container">
                    <?php foreach ($radia as $radio) : ?>
                        <?php
                        $stream_url = get_post_meta($page_id, $radio['meta_key'], true);
                        if ( !empty($stream_url) ) :
                        ?>
                            <div class="radio-player-item" data-stream-url="<?php echo esc_url($stream_url); ?>">
                                <?php // <<<=== ZMĚNA ZDE: Obrázek a název jsou nyní v odkazu ===>>> ?>
                                <a href="<?php echo esc_url($radio['site_url']); ?>" target="_blank" rel="noopener noreferrer" class="radio-info-link">
                                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $radio['icon']); ?>" alt="<?php echo esc_attr($radio['name']); ?>" class="radio-icon">
                                    <h2 class="radio-title"><?php echo esc_html($radio['name']); ?></h2>
                                </a>
                                <button class="radio-play-btn" aria-label="Přehrát <?php echo esc_attr($radio['name']); ?>">
                                    <i class="fa fa-play" aria-hidden="true"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div id="add-radio-button-container">
                    <button id="show-add-radio-form-btn" aria-label="Přidat nové rádio">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>

                <div id="add-radio-form-container" style="display: none;">
                    <form id="custom-radio-form">
                        <h3>Přidat nové rádio</h3>
                        <div class="form-group">
                            <label for="custom-radio-name">Název rádia:</label>
                            <input type="text" id="custom-radio-name" placeholder="Např. Rádio 7" required>
                        </div>
                        <div class="form-group">
                            <label for="custom-radio-stream">URL adresa streamu:</label>
                            <input type="url" id="custom-radio-stream" placeholder="https://icecast.proglas.cz/radio7-128.mp3" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="save-btn">Přidat rádio</button>
                            <button type="button" id="hide-add-radio-form-btn" class="cancel-btn">Zrušit</button>
                        </div>
                    </form>
                </div>
            </div>
        </article>

    </main>
</div>

<?php get_footer(); ?>