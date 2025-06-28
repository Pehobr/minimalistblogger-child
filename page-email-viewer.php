<?php
/**
 * Template Name: Prohlížeč obsahu e-mailu
 * Description: Zobrazí obsah jedné Denní kapky na základě ID z URL.
 */
get_header();

// Získáme ID kapky z URL adresy (např. .../denni-obsah/?kapka_id=123)
$kapka_id = isset($_GET['kapka_id']) ? intval($_GET['kapka_id']) : 0;

// Zkontrolujeme, zda ID existuje a zda je to opravdu "Denní kapka"
if ($kapka_id > 0 && get_post_type($kapka_id) === 'denni_kapka') {
    // Načteme všechna potřebná data z vlastních polí
    $jpii_quote = get_post_meta($kapka_id, 'citat_janpavel', true);
    $benedict_quote = get_post_meta($kapka_id, 'citat_benedikt', true);
    $francis_quote = get_post_meta($kapka_id, 'citat_frantisek', true);
    $leo_quote = get_post_meta($kapka_id, 'citat_lev', true);
    $augustine_quote = get_post_meta($kapka_id, 'citat_augustin', true);
    $prayer = get_post_meta($kapka_id, 'citat_modlitba', true);
    $image_url = get_post_meta($kapka_id, 'image_url', true); // Používáme zkrácený název
    $nazev_dne = get_post_meta($kapka_id, 'nazev_dne', true);
    ?>
    <div id="primary" class="content-area" style="width: 80%; margin: 20px auto;">
        <main id="main" class="site-main">
            <article class="post">
                <header class="entry-header">
                    <h1 class="entry-title"><?php echo esc_html($nazev_dne); ?></h1>
                </header>
                <div class="entry-content">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($nazev_dne); ?>" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                    <?php endif; ?>

                    <h2>Papež Jan Pavel II.</h2>
                    <div><?php echo wpautop(esc_html($jpii_quote)); ?></div>

                    <h2>Papež Benedikt XVI.</h2>
                    <div><?php echo wpautop(esc_html($benedict_quote)); ?></div>

                    <h2>Papež František</h2>
                    <div><?php echo wpautop(esc_html($francis_quote)); ?></div>

                    <h2>Papež Lev XIII.</h2>
                    <div><?php echo wpautop(esc_html($leo_quote)); ?></div>

                    <h2>Sv. Augustin</h2>
                    <div><?php echo wpautop(esc_html($augustine_quote)); ?></div>

                    <h2>Modlitba</h2>
                    <div><?php echo wpautop(esc_html($prayer)); ?></div>
                </div>
            </article>
        </main>
    </div>
    <?php
} else {
    // Pokud ID v URL chybí nebo je neplatné, zobrazí se chybová hláška
    echo '<div id="primary" class="content-area"><main id="main" class="site-main"><p>Obsah nenalezen. Zkontrolujte prosím odkaz.</p></main></div>';
}

get_footer();
?>