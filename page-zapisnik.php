<?php
/**
 * Template Name: Zápisník
 * Description: Stránka pro psaní a správu uživatelských poznámek.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">

        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Můj zápisník</h1>
            </header>

            <div class="entry-content">

                <div id="zapisnik-editor-container">
                    <form id="zapisnik-form">
                        <textarea id="zapisnik-textarea" placeholder="Napište nebo vložte svoji poznámku..."></textarea>
                        <button type="submit" id="zapisnik-save-btn">
                            <i class="fa fa-floppy-o"></i> Uložit poznámku
                        </button>
                    </form>
                </div>

                <div id="zapisnik-actions">
                    <button id="show-notes-btn" class="zapisnik-action-btn">
                        <i class="fa fa-history"></i> Moje zápisky
                    </button>
                    <button id="export-notes-btn" class="zapisnik-action-btn">
                        <i class="fa fa-download"></i> Exportovat do .TXT
                    </button>
                    <button id="copy-all-notes-btn" class="zapisnik-action-btn">
                        <i class="fa fa-copy"></i> Kopírovat vše
                    </button>
                </div>

                <div id="zapisky-container" style="display: none;">
                    </div>

            </div>
        </article>

    </main>
</div>

<?php

get_sidebar(); // <-- PŘIDANÝ ŘÁDEK
get_footer();
?>