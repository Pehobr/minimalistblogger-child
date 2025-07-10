<?php
/**
 * Funkce pro administraci WordPressu.
 * VERZE 6: Přidáno nastavení pro vzhled úvodní stránky.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Registruje stránky s nastavením.
 */
function pehobr_register_settings_page() {
    add_menu_page(
        'Nastavení aplikace',
        'Postní kapky',
        'manage_options',
        'pehobr-app-settings',
        'pehobr_render_settings_page_content',
        'dashicons-admin-generic',
        20
    );
    // === PŘIDANÝ BLOK ZDE ===
    add_submenu_page(
        'pehobr-app-settings',
        'Vzhled úvodní stránky',
        'Vzhled úvodní stránky',
        'manage_options',
        'pehobr-home-layout-settings',
        'pehobr_render_home_layout_page'
    );
    // === KONEC PŘIDANÉHO BLOKU ===
    add_submenu_page(
        'pehobr-app-settings',
        'Nastavení Youtube playlistů',
        'Youtube playlisty',
        'manage_options',
        'pehobr-youtube-settings',
        'pehobr_render_youtube_settings_page'
    );
    add_submenu_page(
        'pehobr-app-settings',
        'Nastavení internetových rádií',
        'Internetová rádia',
        'manage_options',
        'pehobr-radio-settings',
        'pehobr_render_radio_settings_page'
    );
     add_submenu_page(
        'pehobr-app-settings',
        'Pořadí návodů',
        'Pořadí návodů',
        'manage_options',
        'nastaveni-poradi-navodu',
        'pehobr_render_sort_navody_page'
    );
}
add_action( 'admin_menu', 'pehobr_register_settings_page' );


function pehobr_register_settings() {
    register_setting( 'pehobr_app_options_group', 'start_date_setting', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '2026-02-18', ) );
    register_setting( 'pehobr_app_options_group', 'pehobr_show_donation_popup', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', ) );
}
add_action( 'admin_init', 'pehobr_register_settings' );

function pehobr_render_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Nastavení aplikace Postní kapky</h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'pehobr_app_options_group' ); do_settings_sections( 'pehobr-app-settings' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"> <label for="start_date_setting">Datum začátku (Popeleční středa):</label> </th>
                    <td> 
                        <input type="date" id="start_date_setting" name="start_date_setting" value="<?php echo esc_attr( get_option( 'start_date_setting', '2026-02-18' ) ); ?>" /> 
                        <p class="description">Určuje, od jakého data se začne den po dni zobrazovat obsah.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Prosba o dar</th>
                    <td>
                        <label for="pehobr_show_donation_popup">
                            <input type="checkbox" id="pehobr_show_donation_popup" name="pehobr_show_donation_popup" <?php checked( get_option('pehobr_show_donation_popup'), 'on' ); ?> />
                            Zobrazit na úvodní stránce vyskakovací okno s prosbou o dar.
                        </label>
                        <p class="description">Pokud je zaškrtnuto, okno se zobrazí každému uživateli jednou při prvním spuštění aplikace.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Uložit změny' ); ?>
        </form>
    </div>
    <?php
}