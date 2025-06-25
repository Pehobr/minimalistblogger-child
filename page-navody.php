<?php
/**
 * Template Name: Seznam návodů
 * Description: Šablona pro zobrazení přehledu všech návodů formou tlačítek/odkazů.
 * VERZE 2: Opraveno zobrazování obsahu.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">
        <article class="page">
            <div class="entry-content">
                <?php
                // Smyčka, která načte obsah stránky
                while ( have_posts() ) :
                    the_post();
                    
                    // Zobrazí POUZE obsah z editoru, bez jakýchkoliv tlačítek navíc
                    the_content();

                endwhile; 
                ?>
            </div>
        </article>
    </main>
</div>

<?php
get_footer();