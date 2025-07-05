<?php
/**
 * Template Name: Papez Lev App
 * Description: Zobrazí stránku s webem papeže Lva v iframe.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="iframe-container">
            <iframe src="https://papezlev.audiokatechismus.cz/" frameborder="0" allowfullscreen></iframe>
        </div>
        
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="back-to-home-btn" aria-label="Zpět na domovskou stránku">
            <i class="fa fa-arrow-left"></i>
            <span class="back-btn-text">Zpět na Postní kapky</span>
        </a>
    </main>
</div>

<?php
get_footer();
?>