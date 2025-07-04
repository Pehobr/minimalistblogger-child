<?php
/**
 * Template Name: Prezentace
 *
 * Description: Zobrazí vloženou prezentaci z Gamma.app.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">
        <article class="page">
            <div class="entry-content">
                <div class="presentation-container">
                    <iframe src="https://gamma.app/embed/nzgdhmi5fffsf0m" allow="fullscreen" title="sv. Jan Pavel II."></iframe>
                </div>
                <?php
                // Pokud budete chtít pod prezentaci přidat další text z editoru WordPressu, zobrazí se zde.
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