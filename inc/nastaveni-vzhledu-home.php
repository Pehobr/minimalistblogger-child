<?php
/**
 * Správa nastavení pro úvodní stránku v administraci WordPressu.
 * Umožňuje řadit a skrývat sekce na úvodní stránce.
 * VERZE 2: Přidána volba pro zobrazení sekce papežů.
 */

// --- Registrace menu v administraci ---
function pehobr_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=denni_kapka',
        'Vzhled úvodní stránky',
        'Vzhled úvodní stránky',
        'manage_options',
        'pehobr-home-layout',
        'pehobr_home_layout_page_callback'
    );
}
add_action('admin_menu', 'pehobr_add_admin_menu');

// --- Definice dostupných sekcí ---
function pehobr_get_home_sections() {
    return [
        'pope_section' => '1. Ikony: Papežové',
        'saints_section' => '2. Ikony: Svatí',
        'actions_section' => '3. Ikony: Akce',
        'desktop_nav_section' => '4. Navigace pro PC',
        'library_section' => '5. Knihovna odkazů',
    ];
}

// --- Callback pro zobrazení stránky nastavení ---
function pehobr_home_layout_page_callback() {
    // Uložení dat, pokud byl formulář odeslán
    if (isset($_POST['pehobr_save_layout_settings']) && check_admin_referer('pehobr_save_layout_settings_nonce')) {
        // Uložení pořadí
        if (isset($_POST['pehobr_home_layout_order'])) {
            $sanitized_order = array_map('sanitize_text_field', $_POST['pehobr_home_layout_order']);
            update_option('pehobr_home_layout_order', $sanitized_order);
        }
        // Uložení viditelnosti
        $all_sections = pehobr_get_home_sections();
        $visibility = [];
        foreach (array_keys($all_sections) as $slug) {
            $visibility[$slug] = isset($_POST['pehobr_home_section_visibility'][$slug]) ? 'on' : 'off';
        }
        update_option('pehobr_home_section_visibility', $visibility);

        // Uložení zobrazení sekce papežů
        $pope_display = isset($_POST['pehobr_pope_section_display']) ? sanitize_text_field($_POST['pehobr_pope_section_display']) : 'graficke';
        update_option('pehobr_pope_section_display', $pope_display);

        echo '<div class="notice notice-success is-dismissible"><p>Nastavení uloženo.</p></div>';
    }

    // Načtení uložených hodnot
    $all_sections = pehobr_get_home_sections();
    $layout_order = get_option('pehobr_home_layout_order', array_keys($all_sections));
    $visibility = get_option('pehobr_home_section_visibility', array_fill_keys(array_keys($all_sections), 'on'));
    $pope_display_option = get_option('pehobr_pope_section_display', 'graficke');
    ?>
    <div class="wrap">
        <h1>Nastavení vzhledu úvodní stránky</h1>
        <p>Zde můžete přizpůsobit pořadí a viditelnost jednotlivých sekcí na úvodní stránce.</p>

        <form method="post" action="">
            <?php wp_nonce_field('pehobr_save_layout_settings_nonce'); ?>

            <div id="layout-settings-container">
                
                <!-- Nastavení zobrazení sekce papežů -->
                <div class="settings-section">
                    <h2>Zobrazení sekce papežů</h2>
                    <p>Vyberte, jak se má ve výchozím stavu zobrazovat sekce s citáty papežů.</p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Výchozí zobrazení</th>
                            <td>
                                <label>
                                    <input type="radio" name="pehobr_pope_section_display" value="graficke" <?php checked($pope_display_option, 'graficke'); ?>>
                                    Grafické (ikony s vyskakovacím oknem)
                                </label><br>
                                <label>
                                    <input type="radio" name="pehobr_pope_section_display" value="textove" <?php checked($pope_display_option, 'textove'); ?>>
                                    Textové (nadpisy a citáty přímo na stránce)
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Nastavení pořadí a viditelnosti -->
                <div class="settings-section">
                    <h2>Pořadí a viditelnost sekcí</h2>
                    <p>Přetáhněte sekce pro změnu jejich pořadí. Pomocí zaškrtávacího políčka můžete sekci skrýt nebo zobrazit.</p>
                    <ul id="sortable-layout">
                        <?php
                        foreach ($layout_order as $slug) {
                            if (isset($all_sections[$slug])) {
                                $is_visible = isset($visibility[$slug]) && $visibility[$slug] === 'on';
                                ?>
                                <li class="ui-state-default">
                                    <span class="dashicons dashicons-move"></span>
                                    <label>
                                        <input type="checkbox" name="pehobr_home_section_visibility[<?php echo esc_attr($slug); ?>]" <?php checked($is_visible); ?>>
                                        <?php echo esc_html($all_sections[$slug]); ?>
                                    </label>
                                    <input type="hidden" name="pehobr_home_layout_order[]" value="<?php echo esc_attr($slug); ?>">
                                </li>
                                <?php
                            }
                        }
                        // Přidání sekcí, které ještě nejsou v pořadí (pro případ aktualizace)
                        foreach ($all_sections as $slug => $name) {
                            if (!in_array($slug, $layout_order)) {
                                $is_visible = isset($visibility[$slug]) && $visibility[$slug] === 'on';
                                ?>
                                 <li class="ui-state-default">
                                    <span class="dashicons dashicons-move"></span>
                                    <label>
                                        <input type="checkbox" name="pehobr_home_section_visibility[<?php echo esc_attr($slug); ?>]" <?php checked($is_visible); ?>>
                                        <?php echo esc_html($name); ?>
                                    </label>
                                    <input type="hidden" name="pehobr_home_layout_order[]" value="<?php echo esc_attr($slug); ?>">
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <?php submit_button('Uložit nastavení', 'primary', 'pehobr_save_layout_settings'); ?>
        </form>
    </div>
    <style>
        #layout-settings-container { display: flex; gap: 30px; }
        .settings-section { flex: 1; }
        #sortable-layout { list-style: none; margin: 0; padding: 0; width: 100%; }
        #sortable-layout li { margin: 5px 0; padding: 10px; background: #fff; border: 1px solid #ccc; cursor: move; display: flex; align-items: center; gap: 10px; }
        #sortable-layout li label { flex-grow: 1; }
    </style>
    <?php
}

// --- Načtení potřebných skriptů pro administraci ---
function pehobr_load_admin_scripts($hook) {
    if ($hook != 'denni_kapka_page_pehobr-home-layout') {
        return;
    }
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('admin-home-reorder', get_stylesheet_directory_uri() . '/js/admin-home-reorder.js', ['jquery', 'jquery-ui-sortable'], '1.1', true);
}
add_action('admin_enqueue_scripts', 'pehobr_load_admin_scripts');

?>
