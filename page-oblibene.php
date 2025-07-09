<?php
/**
 * Template Name: Oblíbené texty
 * Description: Zobrazí seznam oblíbených textů uložených v lokálním úložišti.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">

        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Oblíbené texty</h1>
            </header>

            <div class="entry-content">

                <div id="favorites-actions">
                    <button id="export-favorites-btn" class="favorite-action-btn">
                        <i class="fa fa-download"></i> Exportovat do .TXT
                    </button>
                    <button id="copy-favorites-btn" class="favorite-action-btn">
                        <i class="fa fa-copy"></i> Kopírovat vše
                    </button>
                </div>
                <div id="favorites-container">
                    </div>

            </div>
        </article>

    </main>
</div>
<?php

get_sidebar(); // <-- PŘIDANÝ ŘÁDEK
get_footer();
?>