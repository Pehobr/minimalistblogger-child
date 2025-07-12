<?php
/**
 * Správa nastavení pro úvodní stránku v administraci WordPressu.
 * Umožňuje řadit a skrývat sekce na úvodní stránce.
 * VERZE 2: Přidána vlastní registrace do admin menu pro zajištění načtení.
 */

// Zabráníme přímému přístupu
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Přidá stránku s nastavením vzhledu do admin menu.
 * Používá prioritu 30, aby se zajistilo, že hlavní menu již existuje.
 */
function pehobr_add_home_layout_submenu_page() {
    add_submenu_page(
        'pehobr-app-settings',           // Slug rodičovského menu
        'Vzhled úvodní stránky',         // Titulek stránky
        'Vzhled úvodní stránky',         // Název v menu
        'manage_options',                // Oprávnění
        'pehobr-home-layout-settings',   // Slug této stránky
        'pehobr_render_home_layout_settings_page' // Funkce pro vykreslení obsahu
    );
}
add_action( 'admin_menu', 'pehobr_add_home_layout_submenu_page', 30 );


/**
 * Definice dostupných sekcí, které lze spravovat.
 *
 * @return array Asociativní pole, kde klíč je slug a hodnota je název sekce.
 */
function pehobr_get_home_layout_sections() {
    return [
        'pope_section'        => 'Řádek: Papežové (Papež, Benedikt, František)',
        'saints_section'      => 'Řádek: sv.Augustin, erb, Lev XIV.)',
        'actions_section'     => 'Řádek: Modlitba, Bible, Inspirace',
        'desktop_nav_section' => 'Řádek: Navigace pro PC (Oblíbené, Archiv, Fotogalerie, Zápisník)',
        'library_section'     => 'Řádek: Knihovny (Video, Audio, Rádio, Podcast)',
    ];
}

/**
 * Vykreslí obsah stránky pro nastavení vzhledu úvodní stránky.
 */
function pehobr_render_home_layout_settings_page() {
    $all_sections = pehobr_get_home_layout_sections();
    $layout_order = get_option('pehobr_home_layout_order', array_keys($all_sections));
    $visibility = get_option('pehobr_home_section_visibility', array_fill_keys(array_keys($all_sections), 'on'));
    ?>
    <div class="wrap">
        <h1>Vzhled úvodní stránky</h1>
        <p>Přetáhněte řádky myší pro změnu jejich pořadí. Změny se ukládají automaticky.</p>
        <p>Pomocí přepínače vpravo můžete daný řádek dočasně skrýt z úvodní stránky.</p>

        <ul id="sortable-home-sections">
            <?php
            // Zobrazíme sekce ve uloženém pořadí
            foreach ($layout_order as $slug) {
                if (isset($all_sections[$slug])) {
                    $is_visible = isset($visibility[$slug]) && $visibility[$slug] === 'on';
                    ?>
                    <li id="<?php echo esc_attr($slug); ?>" class="sortable-item">
                        <span class="dashicons dashicons-move handle"></span>
                        <span class="section-name"><?php echo esc_html($all_sections[$slug]); ?></span>
                        <label class="switch">
                            <input type="checkbox" class="visibility-toggle" <?php checked($is_visible); ?>>
                            <span class="slider round"></span>
                        </label>
                    </li>
                    <?php
                }
            }
            // Zobrazíme nové sekce, které ještě nejsou v pořadí
            foreach ($all_sections as $slug => $name) {
                if (!in_array($slug, $layout_order)) {
                     $is_visible = isset($visibility[$slug]) && $visibility[$slug] === 'on';
                    ?>
                    <li id="<?php echo esc_attr($slug); ?>" class="sortable-item">
                        <span class="dashicons dashicons-move handle"></span>
                        <span class="section-name"><?php echo esc_html($name); ?></span>
                         <label class="switch">
                            <input type="checkbox" class="visibility-toggle" <?php checked($is_visible); ?>>
                            <span class="slider round"></span>
                        </label>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
        <div id="reorder-feedback" style="display:none; margin-top: 15px;"></div>
    </div>
    <style>
        #sortable-home-sections { list-style-type: none; margin: 20px 0; padding: 0; max-width: 800px; }
        .sortable-item { margin: 0 0 8px 0; padding: 12px 18px; font-size: 16px; background-color: #fff; border: 1px solid #ccc; cursor: move; display: flex; align-items: center; border-radius: 4px; }
        .sortable-item .handle { margin-right: 15px; color: #888; }
        .sortable-item .section-name { flex-grow: 1; }
        .ui-state-highlight { height: 50px; background-color: #f0f0f0; border: 1px dashed #ccc; margin-bottom: 8px; }
        .switch { position: relative; display: inline-block; width: 50px; height: 28px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
        .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; }
        input:checked + .slider { background-color: #2271b1; }
        .slider.round { border-radius: 28px; }
        .slider.round:before { border-radius: 50%; }
    </style>
    <?php
}


/**
 * AJAX handler pro uložení pořadí a viditelnosti sekcí.
 */
add_action('wp_ajax_pehobr_save_home_layout_settings', function() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pehobr_home_reorder_nonce' ) ) {
        wp_send_json_error( 'Chyba zabezpečení.', 403 );
    }
    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error( 'Nedostatečná oprávnění.', 403 );
    }

    if (isset($_POST['order']) && is_array($_POST['order'])) {
        $sanitized_order = array_map('sanitize_text_field', $_POST['order']);
        update_option('pehobr_home_layout_order', $sanitized_order);
    }
    
    if (isset($_POST['visibility']) && is_array($_POST['visibility'])) {
        $sanitized_visibility = array_map('sanitize_text_field', $_POST['visibility']);
        update_option('pehobr_home_section_visibility', $sanitized_visibility);
    }

    wp_send_json_success('Nastavení uloženo.');
});