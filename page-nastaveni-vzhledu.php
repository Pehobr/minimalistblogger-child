<?php
/**
 * Template Name: Nastavení vzhledu pro uživatele
 * Description: Umožňuje uživatelům nastavit si, které sekce chtějí vidět a jak se mají zobrazovat.
 * VERZE 4: Přidán globální přepínač barevnosti.
 * @package minimalistblogger-child
 */

get_header();

// Načteme si definice sekcí, které máme v administraci
$all_sections = function_exists('pehobr_get_home_layout_sections') ? pehobr_get_home_layout_sections() : [];

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Nastavení vzhledu úvodní stránky</h1>
            </header>
            <div class="entry-content">

                <?php if (!empty($all_sections)) : ?>
                    <div id="user-layout-settings">

                        <button class="accordion-btn">Vzhled boxů</button>
                        <div class="accordion-content">
                            <div class="setting-item setting-item-pope-display">
                                <label for="toggle-pope-display" class="setting-label">Citáty papežů</label>
                                <div class="toggle-container">
                                    <span class="toggle-label">Obrázky</span>
                                    <label class="switch">
                                        <input type="checkbox" class="display-toggle" id="toggle-pope-display" data-section-slug="pope_section_display">
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="toggle-label">Text</span>
                                </div>
                            </div>
                        </div>
                        
                        <button class="accordion-btn">Barevnost boxů</button>
                        <div class="accordion-content">
                            <div class="setting-item setting-item-global-theme">
                                <label for="toggle-global-theme" class="setting-label">Zobrazit fialové pozadí boxů</label>
                                <label class="switch">
                                    <input type="checkbox" class="theme-toggle" id="toggle-global-theme" data-setting-type="global-theme">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <button class="accordion-btn">Skrytí boxů</button>
                        <div class="accordion-content">
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

                    </div>
                <?php else : ?>
                    <p>Chyba: Nepodařilo se načíst definice sekcí.</p>
                <?php endif; ?>

            </div>
        </article>
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>