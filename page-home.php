<?php
/**
 * Template Name: Úvodní stránka aplikace Home
 * Description: Speciální úvodní stránka pro aplikaci "Postní kapky" s mřížkou ikon a efektem kapek.
 *
 * @package minimalistblogger-child
 */

get_header(); // Načte hlavičku WordPressu
?>

<div id="primary" class="featured-content content-area intro-app">
    <main id="main" class="site-main">

        <div id="intro-wrapper">
            <div id="intro-grid-container">
                
                <?php
                // Získáme ID aktuální stránky pro načtení vlastních polí
                $page_id = get_the_ID();
                ?>

                <?php $quote_frantisek = get_post_meta($page_id, 'citat_janpavel', true); ?>
                <a href="<?php echo empty($quote_frantisek) ? esc_url(home_url('/papez-frantisek/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_frantisek)) { echo 'data-quote="' . esc_attr($quote_frantisek) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-janpavel.png'); ?>" alt="Papež František">
                </a>

                <?php $quote_lev = get_post_meta($page_id, 'citat_benedikt', true); ?>
                <a href="<?php echo empty($quote_lev) ? esc_url(home_url('/papez-lev-xiii/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_lev)) { echo 'data-quote="' . esc_attr($quote_lev) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-benedikt.png'); ?>" alt="Papež Lev XIII.">
                </a>

                <?php $quote_augustin = get_post_meta($page_id, 'citat_frantisek', true); ?>
                <a href="<?php echo empty($quote_augustin) ? esc_url(home_url('/svaty-augustin/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_augustin)) { echo 'data-quote="' . esc_attr($quote_augustin) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-frantisek.png'); ?>" alt="Svatý Augustin">
                </a>

                <?php $quote_modlitba = get_post_meta($page_id, 'citat_modlitba', true); ?>
                <a href="<?php echo empty($quote_modlitba) ? esc_url(home_url('/modlitba/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_modlitba)) { echo 'data-quote="' . esc_attr($quote_modlitba) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-modlitba.png'); ?>" alt="Modlitba">
                </a>

                <?php $quote_zamysleni = get_post_meta($page_id, 'citat_lev', true); ?>
                <a href="<?php echo empty($quote_zamysleni) ? esc_url(home_url('/duchovni-zamysleni/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_zamysleni)) { echo 'data-quote="' . esc_attr($quote_zamysleni) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-lev.png'); ?>" alt="Zamyšlení">
                </a>

                <?php $quote_citaty = get_post_meta($page_id, 'citat_citaty', true); ?>
                <a href="<?php echo empty($quote_citaty) ? esc_url(home_url('/citaty/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_citaty)) { echo 'data-quote="' . esc_attr($quote_citaty) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-citaty.png'); ?>" alt="Citáty">
                </a>

                <?php $quote_svatost = get_post_meta($page_id, 'citat_svatost', true); ?>
                <a href="<?php echo empty($quote_svatost) ? esc_url(home_url('/svatost/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_svatost)) { echo 'data-quote="' . esc_attr($quote_svatost) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-svatost.png'); ?>" alt="Svatost">
                </a>

                <?php $quote_texty = get_post_meta($page_id, 'citat_texty', true); ?>
                <a href="<?php echo empty($quote_texty) ? esc_url(home_url('/nabozenske-texty/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_texty)) { echo 'data-quote="' . esc_attr($quote_texty) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-texty.png'); ?>" alt="Texty">
                </a>

                <?php $quote_komunita = get_post_meta($page_id, 'citat_komunita', true); ?>
                <a href="<?php echo empty($quote_komunita) ? esc_url(home_url('/komunita/')) : '#'; ?>" 
                   class="icon-grid-item" 
                   <?php if (!empty($quote_komunita)) { echo 'data-quote="' . esc_attr($quote_komunita) . '"'; } ?>>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/ikona-komunita.png'); ?>" alt="Komunita">
                </a>

            </div>
        </div>

    </main>
</div>

<div id="quote-modal-overlay" class="quote-modal-overlay"></div>
<div id="quote-modal-container" class="quote-modal-container">
    <button id="quote-modal-close-btn" class="quote-modal-close-btn">&times;</button>
    <div id="quote-modal-content" class="quote-modal-content">
        </div>
</div>

<?php get_footer(); // Načte patičku WordPressu ?>