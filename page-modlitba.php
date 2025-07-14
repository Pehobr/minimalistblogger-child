<?php
/**
 * Template Name: Stránka pro denní modlitbu
 * Popis: Zobrazuje text zamyšlení a audio přehrávač pro denní modlitbu z denních kapek.
 * VERZE 3: Oprava názvu volby pro datum začátku.
 */

get_header(); 
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main modlitba-container" role="main">

        <?php
        // 1. Získání data začátku postní doby z nastavení v administraci
        // OPRAVA: Používáme správný název volby 'start_date_setting'
        $start_date_str = get_option('start_date_setting');
        
        $post_to_display = null;
        $admin_info = ''; // Proměnná pro diagnostické zprávy

        if ($start_date_str) {
            try {
                // 2. Výpočet, kolikátý den od začátku dnes je
                $start_date = new DateTime($start_date_str);
                $today = new DateTime(date('Y-m-d', current_time('timestamp')));
                
                if ($today >= $start_date) {
                    $interval = $start_date->diff($today);
                    $day_index = $interval->days; 

                    // 3. Načtení správného příspěvku v pořadí
                    $args = array(
                        'post_type'      => 'denni_kapka',
                        'post_status'    => 'publish',
                        'posts_per_page' => 1,
                        'offset'         => $day_index,
                        'orderby'        => 'date',
                        'order'          => 'ASC',
                    );

                    $denni_kapka_query = new WP_Query($args);

                    if ($denni_kapka_query->have_posts()) {
                        $post_to_display = $denni_kapka_query->posts[0];
                    }

                    // Sestavení diagnostické zprávy pro admina
                    $admin_info = "Datum začátku: " . htmlspecialchars($start_date_str) . "<br>";
                    $admin_info .= "Dnes je " . ($day_index + 1) . ". den v pořadí. Hledám příspěvek s indexem {$day_index}.<br>";
                    $admin_info .= $post_to_display ? "Příspěvek nalezen: ID " . $post_to_display->ID : "Pro tento den nebyl v databázi nalezen žádný příspěvek.";

                } else {
                    $admin_info = "Postní doba ještě nezačala (nastavený začátek: " . htmlspecialchars($start_date_str) . ").";
                }

            } catch (Exception $e) {
                $admin_info = "Chyba při zpracování data: " . $e->getMessage();
            }
        } else {
            $admin_info = "Datum začátku postní doby není v administraci nastaveno. (Hledaná volba: 'start_date_setting')";
        }

        // 4. Zobrazení obsahu nebo chybové hlášky
        if ($post_to_display) {
            setup_postdata($post_to_display);

            $modlitba_text = get_field('modlitba_text', $post_to_display->ID);
            $modlitba_url = get_field('modlitba_url', $post_to_display->ID);

            ?>
            <article id="post-<?php echo $post_to_display->ID; ?>" <?php post_class('', $post_to_display->ID); ?>>
                <header class="entry-header">
                    <h1 class="entry-title">Zamyšlení a modlitba</h1>
                </header>
                <div class="entry-content">
                    <?php if ($modlitba_text) : ?>
                        <div class="modlitba-text-content">
                            <?php echo wpautop($modlitba_text); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($modlitba_url) : ?>
                        <div class="modlitba-audio-player">
                            <audio id="modlitba-player" controls src="<?php echo esc_url($modlitba_url); ?>">
                                Váš prohlížeč nepodporuje přehrávání audia.
                            </audio>
                        </div>
                    <?php else: ?>
                        <p>Pro dnešní den není k dispozici žádná audio nahrávka.</p>
                    <?php endif; ?>
                </div>
            </article>
            <?php
            wp_reset_postdata();

        } else {
            ?>
            <div class="no-content-found">
                <h2>Obsah nenalezen</h2>
                <p>Omlouváme se, ale pro dnešní den se nepodařilo nalézt žádné zamyšlení. Zkuste to prosím později.</p>
            </div>
            <?php
        }
        
        // Zobrazení diagnostického bloku pouze pro přihlášeného administrátora
        if (current_user_can('manage_options') && !empty($admin_info)) {
            ?>
            <div style="background-color: #e3f2fd; border: 1px solid #b3e5fc; padding: 15px; margin-top: 20px; border-radius: 5px; color: #01579b; font-family: monospace;">
                <strong>Info pro administrátora:</strong><br>
                <?php echo $admin_info; ?>
            </div>
            <?php
        }
        ?>

    </main>
</div>

<?php
get_footer();
?>