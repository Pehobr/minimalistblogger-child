<?php
/**
 * Vytvoří stránku v administraci pro snadné řazení sekcí na úvodní stránce.
 */

// Zabráníme přímému přístupu
if ( ! defined( 'ABSPATH' ) ) exit;

// Registrace nastavení pro ukládání pořadí
add_action('admin_init', function() {
    register_setting('pehobr_home_layout_group', 'pehobr_home_layout_order');
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
        <p>Přetáhněte položky myší a změňte tak jejich pořadí na úvodní stránce. Změny se ukládají automaticky.</p>

        <?php
        $all_sections = pehobr_get_home_sections();
        $saved_order = get_option('pehobr_home_layout_order');
        
        // Pokud není žádné pořadí uloženo, použijeme výchozí
        if ( empty($saved_order) || !is_array($saved_order) ) {
            $saved_order = array_keys($all_sections);
        }
        
        // Zajistíme, že jsou v poli všechny sekce, pro případ budoucího přidání nové
        $ordered_sections = array_merge(array_flip($saved_order), $all_sections);
        ?>

        <ul id="sortable-home-sections">
            <?php foreach ($ordered_sections as $slug => $name) : ?>
                <?php if (isset($all_sections[$slug])) : // Zobrazíme jen existující sekce ?>
                    <li id="<?php echo esc_attr($slug); ?>" class="ui-state-default">
                        <span class="dashicons dashicons-menu"></span>
                        <?php echo esc_html($name); ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <div id="reorder-feedback" style="display:none; margin-top: 15px;"></div>

        <style>
            #sortable-home-sections { list-style-type: none; margin: 20px 0; padding: 0; max-width: 600px; }
            #sortable-home-sections li { margin: 0 0 5px 0; padding: 10px 15px; font-size: 16px; background-color: #fff; border: 1px solid #ccc; cursor: move; display: flex; align-items: center; border-radius: 4px; }
            #sortable-home-sections li .dashicons { margin-right: 10px; color: #888; }
            .ui-state-highlight { height: 44px; background-color: #f0f0f0; border: 1px dashed #ccc; margin: 0 0 5px 0; }
        </style>
    </div>
    <?php
}

// AJAX handler pro uložení nového pořadí
add_action('wp_ajax_pehobr_save_home_layout_order', function() {
    // Kontrola nonce pro bezpečnost
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pehobr_home_reorder_nonce' ) ) {
        wp_send_json_error( 'Chyba zabezpečení.', 403 );
        return;
    }

    // Kontrola oprávnění uživatele
    if ( current_user_can('manage_options') && isset($_POST['order']) && is_array($_POST['order']) ) {
        $order = array_map('sanitize_text_field', $_POST['order']);
        
        // Uložení pole do databáze
        update_option('pehobr_home_layout_order', $order);
        
        wp_send_json_success('Pořadí bylo úspěšně uloženo.');
    } else {
        wp_send_json_error('Nedostatečná oprávnění nebo neplatná data.');
    }
});