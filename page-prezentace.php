<?php
/**
 * Template Name: Prezentace
 *
 * Description: Zobrazí seznam všech dosavadních prezentací z příspěvků "Denní kapka".
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="featured-content content-area">
    <main id="main" class="site-main">
        <article class="page">
            <div class="entry-content">
                <div class="presentation-list-container">
                    <?php
                    // Dotaz na všechny příspěvky typu 'denni_kapka', seřazené od nejnovějších
                    $args = array(
                        'post_type'      => 'denni_kapka',
                        'posts_per_page' => -1, // Zobrazí všechny
                        'orderby'        => 'date',
                        'order'          => 'DESC', // Nejnovější nahoře
                        'post_status'    => 'publish',
                        // Meta query zajistí, že se načtou jen příspěvky, které mají prezentaci
                        'meta_query'     => array(
                            array(
                                'key'     => 'prezentace_url',
                                'value'   => '',
                                'compare' => '!=',
                            ),
                        ),
                    );
                    $presentations_query = new WP_Query($args);

                    if ($presentations_query->have_posts()) :
                        // Projdeme všechny nalezené příspěvky
                        while ($presentations_query->have_posts()) : $presentations_query->the_post();
                            $prezentace_url = get_post_meta(get_the_ID(), 'prezentace_url', true);
                            $nazev_dne = get_post_meta(get_the_ID(), 'nazev_dne', true);
                            
                            // Pokud je název prázdný, použijeme název příspěvku
                            $prezentace_title = !empty($nazev_dne) ? $nazev_dne : get_the_title();
                            ?>
                            <a href="<?php echo esc_url($prezentace_url); ?>" class="presentation-link-button" target="_blank" rel="noopener noreferrer">
                                <?php echo esc_html($prezentace_title); ?>
                            </a>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        // Zpráva, pokud se nenajde žádná prezentace
                        echo '<p>Nebyly nalezeny žádné dosavadní prezentace.</p>';
                    endif;
                    ?>
                </div>
                <?php
                // Zde se může zobrazit další obsah, pokud ho vložíte do editoru stránky "Prezentace"
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