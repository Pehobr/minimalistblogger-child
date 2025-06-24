<?php
/**
 * Template Name: Úvodní stránka aplikace Home
 * Description: Speciální úvodní stránka pro aplikaci "Postní kapky" s mřížkou ikon a efektem kapek.
 *a
 * @package minimalistblogger-child
 */

get_header(); // Načte hlavičku WordPressu
?>

<div id="primary" class="featured-content content-area intro-app">
    <main id="main" class="site-main">

        <div id="intro-wrapper">
            <div id="intro-grid-container">
                
                <?php $page_id = get_the_ID(); ?>

                <?php
                // Data pro všechny dlaždice, abychom se neopakovali
                $grid_items = [
                    ['name' => 'Sv. Jan Pavel II', 'slug' => 'papez-frantisek', 'icon' => 'ikona-janpavel.png', 'citat_key' => 'citat_janpavel'],
                    ['name' => 'Papež Benedikt XVI.', 'slug' => 'papez-benedikt', 'icon' => 'ikona-benedikt.png', 'citat_key' => 'citat_benedikt'],
                    ['name' => 'Papež František', 'slug' => 'papez-frantisek', 'icon' => 'ikona-frantisek.png', 'citat_key' => 'citat_frantisek'],
                    ['name' => 'Modlitba', 'slug' => 'modlitba', 'icon' => 'ikona-modlitba.png', 'citat_key' => 'citat_modlitba'],
                    ['name' => 'Papež Lev XIV.', 'slug' => 'papez-lev', 'icon' => 'ikona-lev.png', 'citat_key' => 'citat_lev'],
                    ['name' => 'Citáty', 'slug' => 'citaty', 'icon' => 'ikona-citaty.png', 'citat_key' => 'citat_citaty'],
                    ['name' => 'Svatost', 'slug' => 'svatost', 'icon' => 'ikona-svatost.png', 'citat_key' => 'citat_svatost'],
                    ['name' => 'Texty', 'slug' => 'nabozenske-texty', 'icon' => 'ikona-texty.png', 'citat_key' => 'citat_texty'],
                    ['name' => 'Komunita', 'slug' => 'komunita', 'icon' => 'ikona-komunita.png', 'citat_key' => 'citat_komunita'],
                ];

                foreach ($grid_items as $item) :
                    $quote = get_post_meta($page_id, $item['citat_key'], true);
                    $has_quote = !empty($quote);
                    $link_url = home_url('/' . $item['slug'] . '/');
                ?>
                    <a href="<?php echo $has_quote ? '#' : esc_url($link_url); ?>" 
                       class="icon-grid-item"
                       <?php if ($has_quote) : ?>
                           data-quote="<?php echo esc_attr($quote); ?>"
                       <?php endif; ?>
                    >
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/' . $item['icon']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                    </a>
                <?php endforeach; ?>

            </div>
        </div>

    </main>
</div><?php
// Zde již NIC NENÍ, get_sidebar() jsme odstranili
?>

<div id="quote-modal-overlay" class="quote-modal-overlay"></div>
<div id="quote-modal-container" class="quote-modal-container">
    <button id="quote-modal-close-btn" class="quote-modal-close-btn">&times;</button>
    <div id="quote-modal-content" class="quote-modal-content">
        </div>
</div>

<?php get_footer(); // Načte patičku WordPressu ?>