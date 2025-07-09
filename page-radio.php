<?php
/**
 * Template Name: Přehrávač rádií
 * Description: Zobrazí stránku s přehrávačem internetových rádií. Kombinuje rádia z nastavení a uživatelská rádia.
 *
 * @package minimalistblogger-child
 */

get_header();

// Načteme rádia uložená v nastavení "Postní kapky -> Internetová rádia"
$nastavena_radia = get_option('pehobr_internet_radia', array());
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Rádia</h1>
            </header>

            <div class="entry-content">
                <div id="radio-player-container">
                    <?php // Část pro rádia z administrace ?>
                    <?php if ( ! empty( $nastavena_radia ) ) : ?>
                        <?php foreach ( $nastavena_radia as $radio ) : ?>
                            <?php
                            $nazev = $radio['nazev'] ?? 'Neznámé rádio';
                            $stream_url = $radio['stream_url'] ?? '';
                            $image_url = !empty($radio['image_url']) ? $radio['image_url'] : get_stylesheet_directory_uri() . '/img/ikona-vlastni.png';
                            if ( empty($stream_url) ) continue;
                            ?>
                            <div class="radio-player-item" data-stream-url="<?php echo esc_url($stream_url); ?>">
                                <?php // Odkaz je zde ponechán pro případné budoucí využití, ale je neaktivní ?>
                                <a href="#" class="radio-info-link" onclick="return false;">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($nazev); ?>" class="radio-icon">
                                    <h2 class="radio-title"><?php echo esc_html($nazev); ?></h2>
                                </a>
                                <button class="radio-play-btn" aria-label="Přehrát <?php echo esc_attr($nazev); ?>">
                                    <i class="fa fa-play" aria-hidden="true"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php // Zde JavaScript vloží rádia přidaná uživatelem ?>
                </div>

                <?php // Formulář pro přidání vlastního rádia uživatelem ?>
                <div id="add-radio-button-container">
                    <button id="show-add-radio-form-btn" aria-label="Přidat nové rádio">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>

                <div id="add-radio-form-container" style="display: none;">
                    <form id="custom-radio-form">
                        <h3>Přidat vlastní rádio</h3>
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

                 <?php
                // Zobrazí se případný další obsah, pokud ho vložíte do editoru stránky
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

get_sidebar(); // <-- PŘIDANÝ ŘÁDEK
get_footer();
?>