<?php
/**
 * Template Name: Seznam návodů
 * Description: Šablona pro zobrazení přehledu všech návodů formou tlačítek/odkazů.
 * VERZE 3: Přidána logika pro automatické načítání podstránek.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>
            <div class="entry-content">
                <?php
                // Zobrazí obsah napsaný v editoru hlavní stránky "Návody"
                while ( have_posts() ) :
                    the_post();
                    the_content();
                endwhile;

                // Argumenty pro načtení všech podstránek (dětí) této stránky
                $args = array(
                    'post_type'      => 'page',
                    'posts_per_page' => -1,
                    'post_parent'    => get_the_ID(), // Klíčové: načte pouze děti aktuální stránky
                    'orderby'        => 'menu_order', // Seřadí je podle pořadí, jaké nastavíte v administraci
                    'order'          => 'ASC',
                );

                $child_pages = new WP_Query($args);

                if ($child_pages->have_posts()) :
                    echo '<ul class="tutorial-list">'; // Použijeme třídu z vašeho CSS
                    while ($child_pages->have_posts()) : $child_pages->the_post();
                        echo '<li class="tutorial-list-item">';
                        echo '<a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a>';
                        echo '</li>';
                    endwhile;
                    echo '</ul>';
                    wp_reset_postdata(); // Důležité pro obnovení původního dotazu
                endif;
                ?>
            </div>
        </article>
    </main>
</div>

<?php

get_sidebar(); // <-- PŘIDANÝ ŘÁDEK
get_footer();
?>