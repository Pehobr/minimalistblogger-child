<?php
/**
 * Správa nastavení pro úvodní stránku v administraci WordPressu.
 * Umožňuje skrývat sekce na úvodní stránce a měnit jejich režim zobrazení.
 * VERZE 4: Odstraněna funkce pro změnu pořadí sekcí.
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
        'pope_section' => 'Papežové',
        'saints_section' => 'sv.Augustin,Lev XIV.',
        'actions_section' => 'Modlitba,pobožnosti...',
        'desktop_nav_section' => 'Navigace pro PC',
        'library_section' => 'Knihovny (audio, video,...)',
    ];
}

// --- Callback pro zobrazení stránky nastavení ---
function pehobr_home_layout_page_callback() {
    // Uložení dat, pokud byl formulář odeslán
    if (isset($_POST['pehobr_save_layout_settings']) && check_admin_referer('pehobr_save_layout_settings_nonce')) {
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

        // Uložení zobrazení sekce svatých
        $saints_display = isset($_POST['pehobr_saints_section_display']) ? sanitize_text_field($_POST['pehobr_saints_section_display']) : 'graficke';
        update_option('pehobr_saints_section_display', $saints_display);

        echo '<div class="notice notice-success is-dismissible"><p>Nastavení uloženo.</p></div>';
    }

    // Načtení uložených hodnot
    $all_sections = pehobr_get_home_sections();
    $visibility = get_option('pehobr_home_section_visibility', array_fill_keys(array_keys($all_sections), 'on'));
    $pope_display_option = get_option('pehobr_pope_section_display', 'graficke');
    $saints_display_option = get_option('pehobr_saints_section_display', 'graficke');
    ?>
    <div class="wrap">
        <h1>Nastavení vzhledu úvodní stránky</h1>
        <p>Zde můžete přizpůsobit viditelnost a výchozí režim zobrazení jednotlivých sekcí na úvodní stránce.</p>

        <form method="post" action="">
            <?php wp_nonce_field('pehobr_save_layout_settings_nonce'); ?>

            <div id="layout-settings-container">
                
                <div class="settings-section">
                    <h2>Zobrazení sekcí</h2>
                    <p>Vyberte, jak se mají ve výchozím stavu zobrazovat jednotlivé sekce.</p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Sekce Papežové</th>
                            <td>
                                <label>
                                    <input type="radio" name="pehobr_pope_section_display" value="graficke" <?php checked($pope_display_option, 'graficke'); ?>>
                                    Grafické (ikony)
                                </label><br>
                                <label>
                                    <input type="radio" name="pehobr_pope_section_display" value="textove" <?php checked($pope_display_option, 'textove'); ?>>
                                    Textové (přímo zobrazené citáty)
                                </label>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Sekce sv. Augustin a Lev XIV.</th>
                            <td>
                                <label>
                                    <input type="radio" name="pehobr_saints_section_display" value="graficke" <?php checked($saints_display_option, 'graficke'); ?>>
                                    Grafické (ikony)
                                </label><br>
                                <label>
                                    <input type="radio" name="pehobr_saints_section_display" value="textove" <?php checked($saints_display_option, 'textove'); ?>>
                                    Textové (přímo zobrazené citáty)
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="settings-section">
                    <h2>Viditelnost sekcí</h2>
                    <p>Pomocí zaškrtávacího políčka můžete sekci skrýt nebo zobrazit.</p>
                    <ul id="visibility-layout">
                        <?php
                        foreach ($all_sections as $slug => $name) {
                            $is_visible = isset($visibility[$slug]) && $visibility[$slug] === 'on';
                            ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="pehobr_home_section_visibility[<?php echo esc_attr($slug); ?>]" <?php checked($is_visible); ?>>
                                    <?php echo esc_html($name); ?>
                                </label>
                            </li>
                            <?php
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
        #visibility-layout { list-style: none; margin: 0; padding: 0; width: 100%; }
        #visibility-layout li { margin: 5px 0; padding: 10px; background: #fff; border: 1px solid #ccc; display: flex; align-items: center; gap: 10px; }
        #visibility-layout li label { flex-grow: 1; }
    </style>
    <?php
}
?>