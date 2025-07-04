<?php
/**
 * Template Name: Prezentace
 *
 * Description: Zobrazí odkaz na externí prezentaci.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">
        <article class="page">
            <div class="entry-content">
                <div class="presentation-link-container">
                    <a href="https://gamma.app/docs/sv-Jan-Pavel-II-nzgdhmi5fffsf0m" class="presentation-link-button" target="_blank" rel="noopener noreferrer">
                        Otevřít prezentaci: sv. Jan Pavel II.
                    </a>
                </div>
                <?php
                // Pokud budete chtít pod tlačítko přidat další text z editoru WordPressu, zobrazí se zde.
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