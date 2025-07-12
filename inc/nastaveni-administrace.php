<?php
/**
 * Funkce pro administraci WordPressu.
 * Spravuje veškerá nastavení aplikace Postní kapky.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

// =============================================================================
// 1. REGISTRACE MENU A PODMENU V ADMINISTRACI
// =============================================================================

/**
 * Registruje hlavní stránku s nastavením a všechny podstránky.
 */
function pehobr_register_settings_pages() {
    // Hlavní menu
    add_menu_page(
        'Nastavení aplikace',
        'Postní kapky',
        'manage_options',
        'pehobr-app-settings',
        'pehobr_render_main_settings_page_content',
        'dashicons-admin-generic',
        20
    );

    // Podmenu: Vzhled úvodní stránky
    add_submenu_page(
        'pehobr-app-settings',
        'Vzhled úvodní stránky',
        'Vzhled úvodní stránky',
        'manage_options',
        'pehobr-home-layout-settings',
        'pehobr_render_home_layout_settings_page'
    );
    
    // Podmenu: Youtube playlisty
    add_submenu_page(
        'pehobr-app-settings',
        'Nastavení Youtube playlistů',
        'Youtube playlisty',
        'manage_options',
        'pehobr-youtube-settings',
        'pehobr_render_youtube_settings_page'
    );
    
    // Podmenu: Internetová rádia
    add_submenu_page(
        'pehobr-app-settings',
        'Nastavení internetových rádií',
        'Internetová rádia',
        'manage_options',
        'pehobr-radio-settings',
        'pehobr_render_radio_settings_page'
    );
    
    // Podmenu: Pořadí návodů
    add_submenu_page(
        'pehobr-app-settings',
        'Pořadí návodů',
        'Pořadí návodů',
        'manage_options',
        'nastaveni-poradi-navodu',
        'pehobr_render_sort_navody_page'
    );
}
add_action( 'admin_menu', 'pehobr_register_settings_pages' );


// =============================================================================
// 2. HLAVNÍ STRÁNKA NASTAVENÍ (Postní kapky)
// =============================================================================

/**
 * Registruje nastavení pro hlavní stránku.
 */
function pehobr_register_main_settings() {
    register_setting( 'pehobr_app_options_group', 'start_date_setting', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '2026-02-18' ] );
    register_setting( 'pehobr_app_options_group', 'pehobr_show_donation_popup', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ] );
    register_setting( 'pehobr_app_options_group', 'pehobr_pope_nav_style', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => 'svetle' ] );
    register_setting( 'pehobr_app_options_group', 'pehobr_saints_nav_style', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => 'svetle' ] );
    register_setting( 'pehobr_app_options_group', 'pehobr_desktop_nav_style', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => 'svetle' ] );
    register_setting( 'pehobr_app_options_group', 'pehobr_actions_nav_style', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => 'svetle' ] );
    
    // === NOVÝ KÓD PRO BARVY LIŠTY ZAČÍNÁ ZDE ===
    register_setting( 'pehobr_app_options_group', 'pehobr_bottom_nav_bg_color', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_hex_color', 'default' => '#7e7383' ] );
    register_setting( 'pehobr_app_options_group', 'pehobr_bottom_nav_icon_color', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_hex_color', 'default' => '#ffffff' ] );
    register_setting( 'pehobr_app_options_group', 'pehobr_bottom_nav_convex_bg_color', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_hex_color', 'default' => '#ffffff' ] );
    register_setting( 'pehobr_app_options_group', 'pehobr_bottom_nav_active_icon_color', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_hex_color', 'default' => '#7e7383' ] );
    // === NOVÝ KÓD PRO BARVY LIŠTY KONČÍ ZDE ===
}
add_action( 'admin_init', 'pehobr_register_main_settings' );

/**
 * Vykreslí obsah hlavní stránky nastavení.
 */
function pehobr_render_main_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Nastavení aplikace Postní kapky</h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'pehobr_app_options_group' ); do_settings_sections( 'pehobr-app-settings' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="start_date_setting">Datum začátku (Popeleční středa):</label></th>
                    <td>
                        <input type="date" id="start_date_setting" name="start_date_setting" value="<?php echo esc_attr( get_option( 'start_date_setting', '2026-02-18' ) ); ?>" />
                        <p class="description">Určuje, od jakého data se začne den po dni zobrazovat obsah.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Prosba o dar</th>
                    <td>
                        <label for="pehobr_show_donation_popup">
                            <input type="checkbox" id="pehobr_show_donation_popup" name="pehobr_show_donation_popup" value="on" <?php checked( get_option('pehobr_show_donation_popup'), 'on' ); ?> />
                            Zobrazit na úvodní stránce vyskakovací okno s prosbou o dar.
                        </label>
                        <p class="description">Pokud je zaškrtnuto, okno se zobrazí každému uživateli jednou při prvním spuštění aplikace.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Styl řádku 'Papežové' na PC</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Styl řádku 'Papežové' na PC</span></legend>
                            <?php $current_pope_style = get_option('pehobr_pope_nav_style', 'svetle'); ?>
                            <label>
                                <input type="radio" name="pehobr_pope_nav_style" value="svetle" <?php checked($current_pope_style, 'svetle'); ?> />
                                <span>Světlé pozadí, fialové ikony a text (výchozí)</span>
                            </label><br />
                            <label>
                                <input type="radio" name="pehobr_pope_nav_style" value="fialove" <?php checked($current_pope_style, 'fialove'); ?> />
                                <span>Fialové pozadí, fialové ikony a světlý text</span>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                 <tr valign="top">
                    <th scope="row">Styl řádku 'Svatí' na PC</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Styl řádku 'sv.Augustin, Lev XIV.' na PC</span></legend>
                            <?php $current_saints_style = get_option('pehobr_saints_nav_style', 'svetle'); ?>
                            <label>
                                <input type="radio" name="pehobr_saints_nav_style" value="svetle" <?php checked($current_saints_style, 'svetle'); ?> />
                                <span>Světlé pozadí, fialový text (výchozí)</span>
                            </label><br />
                            <label>
                                <input type="radio" name="pehobr_saints_nav_style" value="fialove" <?php checked($current_saints_style, 'fialove'); ?> />
                                <span>Fialové pozadí, světlý text</span>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Styl navigačního řádku na PC</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Styl navigačního řádku na PC</span></legend>
                            <?php $current_style = get_option('pehobr_desktop_nav_style', 'svetle'); ?>
                            <label>
                                <input type="radio" name="pehobr_desktop_nav_style" value="svetle" <?php checked($current_style, 'svetle'); ?> />
                                <span>Světlé pozadí, fialové ikony (výchozí)</span>
                            </label><br />
                            <label>
                                <input type="radio" name="pehobr_desktop_nav_style" value="fialove" <?php checked($current_style, 'fialove'); ?> />
                                <span>Fialové pozadí, světlé ikony</span>
                            </label>
                        </fieldset>
                        <p class="description">Vzhled řádku s ikonami (Oblíbené, Archiv, atd.)</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Styl řádku 'Modlitba, bible, inspirace' na PC</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Styl řádku 'Akce' na PC</span></legend>
                            <?php $current_actions_style = get_option('pehobr_actions_nav_style', 'svetle'); ?>
                            <label>
                                <input type="radio" name="pehobr_actions_nav_style" value="svetle" <?php checked($current_actions_style, 'svetle'); ?> />
                                <span>Světlé pozadí, fialové ikony (výchozí)</span>
                            </label><br />
                            <label>
                                <input type="radio" name="pehobr_actions_nav_style" value="fialove" <?php checked($current_actions_style, 'fialove'); ?> />
                                <span>Fialové pozadí, světlé ikony</span>
                            </label>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Barvy dolní lišty na mobilu</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Barvy dolní lišty na mobilu</span></legend>
                            
                            <label for="pehobr_bottom_nav_bg_color" style="display:block; margin-bottom: 5px;">Barva pozadí lišty:</label>
                            <input type="color" id="pehobr_bottom_nav_bg_color" name="pehobr_bottom_nav_bg_color" value="<?php echo esc_attr( get_option( 'pehobr_bottom_nav_bg_color', '#7e7383' ) ); ?>" />
                            <br><br>

                            <label for="pehobr_bottom_nav_icon_color" style="display:block; margin-bottom: 5px;">Barva ikon a horního ohraničení:</label>
                            <input type="color" id="pehobr_bottom_nav_icon_color" name="pehobr_bottom_nav_icon_color" value="<?php echo esc_attr( get_option( 'pehobr_bottom_nav_icon_color', '#ffffff' ) ); ?>" />
                            <br><br>
                            
                            <label for="pehobr_bottom_nav_convex_bg_color" style="display:block; margin-bottom: 5px;">Barva pozadí prostředního tlačítka (Domů):</label>
                            <input type="color" id="pehobr_bottom_nav_convex_bg_color" name="pehobr_bottom_nav_convex_bg_color" value="<?php echo esc_attr( get_option( 'pehobr_bottom_nav_convex_bg_color', '#ffffff' ) ); ?>" />
                            <br><br>

                            <label for="pehobr_bottom_nav_active_icon_color" style="display:block; margin-bottom: 5px;">Barva aktivní ikony v prostředním tlačítku:</label>
                            <input type="color" id="pehobr_bottom_nav_active_icon_color" name="pehobr_bottom_nav_active_icon_color" value="<?php echo esc_attr( get_option( 'pehobr_bottom_nav_active_icon_color', '#7e7383' ) ); ?>" />
                        </fieldset>
                        <p class="description">Vyberte barvy pro jednotlivé prvky dolní navigační lišty na mobilních zařízeních.</p>
                    </td>
                </tr>
                </table>
            <?php submit_button( 'Uložit změny' ); ?>
        </form>
    </div>
    <?php
}

// =============================================================================
// 3. STRÁNKA PRO VZHLED ÚVODNÍ STRÁNKY
// =============================================================================

/**
 * Definice dostupných sekcí, které lze spravovat na úvodní stránce.
 */
function pehobr_get_home_layout_sections() {
    return [
        'pope_section'        => 'Papežové (Jan Pavel, Benedikt, František)',
        'saints_section'      => 'sv.Augustin, erb, Lev XIV.',
        'actions_section'     => 'Modlitba, Bible, Inspirace',
        'desktop_nav_section' => 'Navigace pro PC (Oblíbené, Archiv, Fotogalerie, Zápisník)',
        'library_section'     => 'Knihovny (Video, Audio,..)',
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
            // Zobrazíme sekce v uloženém pořadí
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
        input:checked + .slider:before { transform: translateX(22px); }
        .slider.round { border-radius: 28px; }
        .slider.round:before { border-radius: 50%; }
    </style>
    <?php
}

/**
 * AJAX handler pro uložení pořadí a viditelnosti sekcí vzhledu.
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