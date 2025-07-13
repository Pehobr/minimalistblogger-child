<?php
/**
 * Template Name: Ikony mobilní lišty
 * Description: Umožňuje uživatelům nastavit si ikony ve spodní mobilní liště.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Nastavení ikon mobilní lišty</h1>
            </header>
            <div class="entry-content">
                <div id="mobile-nav-settings">

                    <button class="accordion-btn">1. Ikona (vlevo)</button>
                    <div class="accordion-content">
                        <p class="settings-description">Vyberte, co se má zobrazit na první pozici v liště.</p>
                        <div class="setting-item" data-nav-position="1">
                            
                            <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="oblibene" data-icon="fa-star" data-url="<?php echo esc_url( home_url('/oblibene-texty/') ); ?>">
                                <i class="fa fa-star"></i> Oblíbené texty
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="archiv" data-icon="fa-folder-open-o" data-url="<?php echo esc_url( home_url('/archiv-citatu/') ); ?>">
                                <i class="fa fa-folder-open-o"></i> Archiv citátů
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="zapisnik" data-icon="fa-pencil" data-url="<?php echo esc_url( home_url('/zapisnik/') ); ?>">
                                <i class="fa fa-pencil"></i> Můj zápisník
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="video" data-icon="fa-video-camera" data-url="<?php echo esc_url( home_url('/video-kapky/') ); ?>">
                                <i class="fa fa-video-camera"></i> Video kapky
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="fotogalerie" data-icon="fa-picture-o" data-url="<?php echo esc_url( home_url('/fotogalerie/') ); ?>">
                                <i class="fa fa-picture-o"></i> Fotogalerie
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="pisne" data-icon="fa-music" data-url="<?php echo esc_url( home_url('/postni-pisne/') ); ?>">
                                <i class="fa fa-music"></i> Postní písně
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="radia" data-icon="fa-headphones" data-url="<?php echo esc_url( home_url('/krestanska-radia') ); ?>">
                                <i class="fa fa-headphones"></i> Internetová rádia
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="podcast" data-icon="fa-podcast" data-url="<?php echo esc_url( home_url('/podcast/') ); ?>">
                                <i class="fa fa-podcast"></i> Podcast
                            </label>

                             <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="modlitba" data-icon="fa-commenting" data-url="<?php echo esc_url( home_url('/') ); ?>">
                                <i class="fa fa-commenting"></i> Modlitba
                            </label>

                             <label class="radio-label">
                                <input type="radio" name="nav-icon-1" value="poboznost" data-icon="fa-book" data-url="<?php echo esc_url( home_url('/poboznosti/') ); ?>">
                                <i class="fa fa-book"></i> Pobožnost
                            </label>

                        </div>
                    </div>

                    <button class="accordion-btn">2. Ikona</button>
                    <div class="accordion-content">
                        <p>Nastavení pro druhou ikonu bude doplněno.</p>
                    </div>

                    <button class="accordion-btn">4. Ikona</button>
                    <div class="accordion-content">
                         <p>Nastavení pro čtvrtou ikonu bude doplněno.</p>
                    </div>

                    <button class="accordion-btn">5. Ikona (vpravo)</button>
                    <div class="accordion-content">
                         <p>Nastavení pro pátou ikonu bude doplněno.</p>
                    </div>

                </div>
            </div>
        </article>
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>