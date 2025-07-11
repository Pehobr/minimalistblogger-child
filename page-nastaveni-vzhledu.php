<?php
/**
 * Template Name: Nastavení vzhledu pro uživatele
 * Description: Umožňuje uživatelům nastavit si, které sekce chtějí vidět a jak se mají zobrazovat.
 * VERZE 2: Přidána volba pro zobrazení sekce papežů.
 * @package minimalistblogger-child
 */

get_header();

// Načteme si definice sekcí, které máme v administraci
$all_sections = function_exists('pehobr_get_home_sections') ? pehobr_get_home_sections() : [];

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Nastavení vzhledu úvodní stránky</h1>
            </header>
            <div class="entry-content">
<p style="font-weight: bold; text-align: center;">VZHLED BLOKŮ ÚVODNÍ STRÁNKY</p>

                <?php if (!empty($all_sections)) : ?>
                    <div id="user-layout-settings">
                        
                        <!-- Volba zobrazení papežů -->
                        <div class="setting-item setting-item-pope-display">
                            <label for="toggle-pope-display" class="setting-label">Citáty papežů</label>
                            <div class="toggle-container">
                                <span class="toggle-label"></span>
                                <label class="switch">
                                    <input type="checkbox" class="display-toggle" id="toggle-pope-display" data-section-slug="pope_section_display">
                                    <span class="slider round"></span>
                                </label>
                                <span class="toggle-label">Text</span>
                            </div>
                        </div>
                        
                        <hr class="settings-divider">

                        <!-- Přepínače viditelnosti -->
                        <?php foreach ($all_sections as $slug => $name) : ?>
                            <div class="setting-item setting-item-<?php echo esc_attr($slug); ?>">
                                <label for="toggle-<?php echo esc_attr($slug); ?>" class="setting-label"><?php echo esc_html($name); ?></label>
                                <label class="switch">
                                    <input type="checkbox" class="visibility-toggle" id="toggle-<?php echo esc_attr($slug); ?>" data-section-slug="<?php echo esc_attr($slug); ?>">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p>Chyba: Nepodařilo se načíst definice sekcí.</p>
                <?php endif; ?>

            </div>
        </article>
    </main>
</div>

<style>
    /* Styly specifické pro tuto stránku */
    #user-layout-settings {
        max-width: 600px;
        margin: 20px auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .setting-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background-color: #f0ebf8;
        border: 1px solid #dcd7e9;
        border-radius: 8px;
    }
    .setting-label {
        color: #3b0f5d;
        font-weight: bold;
        font-family: sans-serif;
    }
    .settings-divider {
        border: none;
        border-top: 1px solid #dcd7e9;
        margin: 10px 0;
    }
    /* Kontejner pro přepínač s textovými popisky */
    .toggle-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .toggle-label {
        font-size: 0.9em;
        color: #555;
    }
    /* Styly pro přepínač (toggle switch) */
    .switch { position: relative; display: inline-block; width: 50px; height: 28px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
    .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; }
    input:checked + .slider { background-color: #2271b1; }
    input:checked + .slider:before { transform: translateX(22px); }
    .slider.round { border-radius: 28px; }
    .slider.round:before { border-radius: 50%; }

    @media (max-width: 768px) {
        .setting-item-desktop_nav_section {
            display: none !important;
        }
    }
</style>

<?php
get_sidebar();
get_footer();
?>
