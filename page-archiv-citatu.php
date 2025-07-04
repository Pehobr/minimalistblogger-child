<?php
/**
 * Template Name: Archiv citátů
 * Description: Zobrazí archiv všech citátů přímo z custom fields denních kapek s možností filtrování a přidávání do oblíbených.
 * VERZE 2.1: Odebrání slova "Papež" a úprava jmen.
 */
get_header(); 

// Definice autorů, jejich custom field klíčů a slugů pro filtrování
$autori = [
    'jan-pavel-ii' => [
        'name' => 'sv. Jan Pavel II.',
        'meta_key' => 'citat_janpavel'
    ],
    'benedikt-xvi' => [
        'name' => 'Benedikt XVI.',
        'meta_key' => 'citat_benedikt'
    ],
    'frantisek' => [
        'name' => 'František',
        'meta_key' => 'citat_frantisek'
    ],
    'lev-xiii' => [
        'name' => 'Lev XIV.',
        'meta_key' => 'citat_lev'
    ],
    'augustin' => [
        'name' => 'sv. Augustin',
        'meta_key' => 'citat_augustin'
    ],
];

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header>

            <div class="entry-content">
                <?php // Tlačítka pro filtrování generovaná z pole $autori ?>
                <div id="citat-filters">
                    <button class="filter-btn active" data-filter="*">Zobrazit vše</button>
                    <?php foreach ( $autori as $slug => $data ) : ?>
                        <button class="filter-btn" data-filter=".<?php echo esc_attr( $slug ); ?>">
                            <?php echo esc_html( $data['name'] ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php
                // Dotaz na všechny Denní kapky
                $args = array(
                    'post_type' => 'denni_kapka',
                    'posts_per_page' => -1,
                    'orderby' => 'date',
                    'order' => 'DESC', // Od nejnovějších
                );
                $citaty_query = new WP_Query( $args );

                if ( $citaty_query->have_posts() ) : ?>
                    <div id="citat-list">
                        <?php 
                        // Projdeme všechny Denní kapky
                        while ( $citaty_query->have_posts() ) : $citaty_query->the_post();
                            $post_id = get_the_ID();

                            // Pro každou Denní kapku projdeme všechny definované autory
                            foreach ($autori as $slug => $autor_data) {
                                $meta_key = $autor_data['meta_key'];
                                $citat_text = get_post_meta($post_id, $meta_key, true);

                                // Pokud pro daného autora existuje citát, zobrazíme ho
                                if ( !empty( trim( $citat_text ) ) ) {
                                    // Vytvoříme unikátní ID pro každý citát (kombinace ID příspěvku a slug autora)
                                    $quote_id = 'quote-' . $post_id . '-' . $slug;
                                    ?>
                                    <div class="citat-item <?php echo esc_attr( $slug ); ?>" id="<?php echo esc_attr($quote_id); ?>">
                                        
                                        <blockquote class="citat-text">
                                            <?php echo wpautop( esc_html( $citat_text ) ); // wpautop pro zachování řádkování ?>
                                        </blockquote>
                                        
                                        <footer class="citat-item-footer">
                                            <button class="archive-favorite-btn" data-id="<?php echo esc_attr($quote_id); ?>" aria-label="Přidat do oblíbených">
                                                <i class="fa fa-star-o"></i>
                                            </button>
                                            <span class="citat-author"><?php echo esc_html( $autor_data['name'] ); ?></span>
                                        </footer>

                                    </div>
                                    <?php
                                }
                            }
                        endwhile;
                        ?>
                    </div>
                    <?php
                    wp_reset_postdata();
                else :
                    echo '<p>Zatím nebyly přidány žádné citáty.</p>';
                endif;
                ?>
            </div>
        </article>

    </main>
</div>

<?php get_footer(); ?>