<?php
/**
 * Vytvoří stránku v administraci pro snadné řazení a zobrazení sekcí na úvodní stránce.
 * VERZE 2: Přidána možnost skrýt/zobrazit sekci.
 */

// Zabráníme přímému přístupu
if ( ! defined( 'ABSPATH' ) ) exit;

// Registrace nastavení pro ukládání pořadí a viditelnosti
add_action('admin_init', function() {
    register_setting('pehobr_home_layout_group', 'pehobr_home_layout_order');
    register_setting('pehobr_home_layout_group', 'pehobr_home_section_visibility'); // Nové nastavení
});

// Funkce, která definuje všechny dostupné sekce
function pehobr_get_home_sections() {
    return [
        'pope_section' => 'Řádek - Jan Pavel, Benedikt, František',
        'saints_section' => 'Řádek - sv. Augustin, erb, Lev XIV',
        'actions_section' => 'Řádek - Modlitba, Bible, Inspirace',
        'desktop_nav_section' => 'Řádek - Oblíbené, Archiv, atd.',
        'library_section' => 'Řádek - knihovny (Video, Audio, Rádio, Podcast)'
    ];
}

// Funkce, která vykreslí obsah stránky pro řazení
function pehobr_render_home_layout_page() {
    ?>
    <div class="wrap">
        <h1>Nastavení vzhledu úvodní stránky</h1>
        <p>Přetáhněte položky myší pro změnu pořadí. Pomocí přepínače můžete sekci skrýt nebo zobrazit. Změny se ukládají automaticky.</p>

        <?php
        $all_sections = pehobr_get_home_sections();
        $saved_order = get_option('pehobr_home_layout_order');
        $visibility_settings = get_option('pehobr_home_section_visibility', array_fill_keys(array_keys($all_sections), 'on'));

        // Pokud není žádné pořadí uloženo, použijeme výchozí
        if ( empty($saved_order) || !is_array($saved_order) ) {
            $saved_order = array_keys($all_sections);
        }
        
        // Zajistíme, že jsou v poli všechny sekce
        $ordered_sections = array_merge(array_flip($saved_order), $all_sections);
        ?>

        <ul id="sortable-home-sections">
            <?php foreach ($ordered_sections as $slug => $name) : ?>
                <?php if (isset($all_sections[$slug])) : 
                    $is_visible = isset($visibility_settings[$slug]) && $visibility_settings[$slug] === 'on';
                ?>
                    <li id="<?php echo esc_attr($slug); ?>" class="ui-state-default">
                        <span class="dashicons dashicons-menu handle"></span>
                        <span class="section-name"><?php echo esc_html($name); ?></span>
                        <label class="switch">
                            <input type="checkbox" class="visibility-toggle" <?php checked($is_visible); ?>>
                            <span class="slider round"></span>
                        </label>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <div id="reorder-feedback" style="display:none; margin-top: 15px;"></div>

        <style>
            #sortable-home-sections { list-style-type: none; margin: 20px 0; padding: 0; max-width: 700px; }
            #sortable-home-sections li { margin: 0 0 5px 0; padding: 10px 15px; font-size: 16px; background-color: #fff; border: 1px solid #ccc; cursor: move; display: flex; align-items: center; border-radius: 4px; }
            #sortable-home-sections li .handle { margin-right: 15px; color: #888; }
            #sortable-home-sections li .section-name { flex-grow: 1; }
            .ui-state-highlight { height: 44px; background-color: #f0f0f0; border: 1px dashed #ccc; margin: 0 0 5px 0; }
            
            /* Styly pro přepínač */
            .switch { position: relative; display: inline-block; width: 50px; height: 28px; flex-shrink: 0; }
            .switch input { opacity: 0; width: 0; height: 0; }
            .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
            .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; }
            input:checked + .slider { background-color: #2271b1; } /* WordPress modrá */
            input:checked + .slider:before { transform: translateX(22px); }
            .slider.round { border-radius: 28px; }
            .slider.round:before { border-radius: 50%; }
        </style>
    </div>
    <?php
}

// AJAX handler pro uložení nového pořadí a viditelnosti
add_action('wp_ajax_pehobr_save_home_layout_settings', function() {
    // Kontrola nonce pro bezpečnost
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pehobr_home_reorder_nonce' ) ) {
        wp_send_json_error( 'Chyba zabezpečení.', 403 );
        return;
    }

    // Kontrola oprávnění uživatele
    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error('Nedostatečná oprávnění.');
        return;
    }

    // Uložení pořadí
    if ( isset($_POST['order']) && is_array($_POST['order']) ) {
        $order = array_map('sanitize_text_field', $_POST['order']);
        update_option('pehobr_home_layout_order', $order);
    }
    
    // Uložení viditelnosti
    if ( isset($_POST['visibility']) && is_array($_POST['visibility']) ) {
        $visibility = array_map('sanitize_text_field', $_POST['visibility']);
        update_option('pehobr_home_section_visibility', $visibility);
    }
    
    wp_send_json_success('Nastavení bylo úspěšně uloženo.');
});