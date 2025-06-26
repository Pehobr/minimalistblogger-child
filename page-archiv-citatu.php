<?php
/**
 * Template Name: Archiv citátů
 * Description: Zobrazí archiv všech citátů s možností filtrování.
 */
get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header>

            <div class="entry-content">
                <?php
                // Získání všech použitých termů z taxonomie 'papez'
                $terms = get_terms( array(
                    'taxonomy' => 'papez',
                    'hide_empty' => true,
                ) );

                // Zobrazení filtrů
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
                ?>
                    <div id="citat-filters">
                        <button class="filter-btn active" data-filter="*">Zobrazit vše</button>
                        <?php foreach ( $terms as $term ) : ?>
                            <button class="filter-btn" data-filter=".<?php echo esc_attr( $term->slug ); ?>">
                                <?php echo esc_html( $term->name ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php
                // WP_Query pro získání všech příspěvků 'denni_kapka'
                $args = array(
                    'post_type' => 'denni_kapka',
                    'posts_per_page' => -1,
                    'orderby' => 'date',
                    'order' => 'DESC',
                );
                $citaty_query = new WP_Query( $args );

                if ( $citaty_query->have_posts() ) :
                ?>
                    <div id="citat-list">
                        <?php while ( $citaty_query->have_posts() ) : $citaty_query->the_post(); ?>
                            <?php
                            // Zkontrolujeme, zda má příspěvek obsah
                            if ( !empty( get_the_content() ) ) :

                                $papez_terms = get_the_terms( get_the_ID(), 'papez' );
                                $papez_classes = '';
                                if ( $papez_terms && ! is_wp_error( $papez_terms ) ) {
                                    foreach( $papez_terms as $term ) {
                                        $papez_classes .= $term->slug . ' ';
                                    }
                                }
                                ?>
                                <div class="citat-item <?php echo esc_attr( $papez_classes ); ?>">
                                    <blockquote class="citat-text">
                                        <?php the_content(); ?>
                                    </blockquote>
                                    <footer class="citat-meta">
                                        <?php if ($papez_terms) : 
                                            echo '<span class="citat-author">' . esc_html( $papez_terms[0]->name ) . '</span>';
                                        endif; ?>
                                    </footer>
                                </div>
                            <?php 
                            endif; // Konec podmínky pro kontrolu obsahu
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