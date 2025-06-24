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
                <a href="<?php echo esc_url( '/zkusebni-stranka/' ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-janpavel.png' ); ?>" alt="Papež František">
                </a>

                <a href="<?php echo esc_url( home_url( '/papez-lev-xiii/' ) ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-benedikt.png' ); ?>" alt="Papež Lev XIII.">
                </a>

                <a href="<?php echo esc_url( home_url( '/svaty-augustin/' ) ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-frantisek.png' ); ?>" alt="Svatý Augustin">
                </a>

                <a href="<?php echo esc_url( home_url( '/modlitba/' ) ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-modlitba.png' ); ?>" alt="Modlitba">
                </a>

                <a href="<?php echo esc_url( home_url( '/duchovni-zamysleni/' ) ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-lev.png' ); ?>" alt="Zamyšlení">
                </a>

                <a href="<?php echo esc_url( home_url( '/citaty/' ) ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-citaty.png' ); ?>" alt="Citáty">
                </a>

                <a href="<?php echo esc_url( home_url( '/svatost/' ) ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-svatost.png' ); ?>" alt="Svatost">
                </a>

                <a href="<?php echo esc_url( home_url( '/nabozenske-texty/' ) ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-texty.png' ); ?>" alt="Texty">
                </a>

                <a href="<?php echo esc_url( home_url( '/komunita/' ) ); ?>" class="icon-grid-item">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/ikona-komunita.png' ); ?>" alt="Komunita">
                </a>

            </div>
        </div>

    </main></div><?php get_footer(); // Načte patičku WordPressu ?>
