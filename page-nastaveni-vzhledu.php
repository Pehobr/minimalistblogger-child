<?php
/**
 * Template Name: Nastavení vzhledu pro uživatele
 * Description: Umožňuje uživatelům nastavit si, které sekce chtějí vidět a jak se mají zobrazovat.
 * VERZE 7: Přidány návrhy barev pro světlé téma.
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

                        <button class="accordion-btn">Vzhled boxů</button>
                        <div class="accordion-content">
                            
                            <div class="setting-item setting-item-pope-display">
                                <label for="toggle-pope-display" class="setting-label">Citáty papežů</label>
                                <div class="toggle-container">
                                    <span class="toggle-label">Grafika</span>
                                    <label class="switch">
                                        <input type="checkbox" class="display-toggle" id="toggle-pope-display" data-section-slug="pope_section_display">
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="toggle-label">Text</span>
                                </div>
                            </div>
                            
                            <div class="setting-item">
                                <label for="light-theme-color-picker" class="setting-label">Barva světlého pozadí</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" id="light-theme-color-picker" value="#f5f2eb">
                                    <div class="color-suggestions">
                                        <?php
                                        // Pole s navrhovanými barvami
                                        $suggested_colors = ['#FFE8FF', '#E8D5B5', '#FFFADE', '#E3EFFD', '#FEF7FF','#f5f2eb'];
                                        foreach ($suggested_colors as $color) {
                                            echo '<button class="color-suggestion-btn" data-color="' . $color . '" style="background-color:' . $color . ';" title="' . $color . '"></button>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="setting-divider">

                            <h3 class="setting-subtitle">Barevnost boxů</h3>
                            
                            <?php
                            $themeable_sections = [
                                'pope_section'        => 'Papežové',
                                'saints_section'      => 'Augustin, Lev',
                                'actions_section'     => 'Modlitba, Bible..',
                                'desktop_nav_section' => 'Navigace pro PC',
                                'library_section'     => 'Knihovny (video..)',
                            ];

                            foreach ($themeable_sections as $slug => $label) :
                            ?>
                                <div class="setting-item setting-item-<?php echo esc_attr($slug); ?>-theme">
                                    <label for="toggle-<?php echo esc_attr($slug); ?>-theme" class="setting-label"><?php echo esc_html($label); ?></label>
                                    <div class="toggle-container">
                                        <span class="toggle-label">Světlé</span>
                                        <label class="switch">
                                            <input type="checkbox" class="theme-toggle" id="toggle-<?php echo esc_attr($slug); ?>-theme" data-section-slug="<?php echo esc_attr($slug); ?>">
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="toggle-label">Fialové</span>
                                    </div>
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