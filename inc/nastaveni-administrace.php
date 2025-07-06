<?php
/**
 * Funkce pro administraci WordPressu.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

function pehobr_register_settings_page() {
    add_menu_page( 'Postní kapky', 'Postní kapky', 'manage_options', 'postni-kapky-menu', null, 'dashicons-drop', 20 );
    add_submenu_page( 'postni-kapky-menu', 'Nastavení začátku doby postní', 'Nastavení data', 'manage_options', 'pehobr-app-settings', 'pehobr_render_settings_page_content' );
    // === PŘIDANÝ ŘÁDEK ZDE ===
    add_submenu_page( 'postni-kapky-menu', 'Nastavení YouTube Playlistů', 'YouTube Playlisty', 'manage_options', 'pehobr-youtube-settings', 'pehobr_render_youtube_settings_page' );
}
add_action( 'admin_menu', 'pehobr_register_settings_page' );

function pehobr_register_settings() {
    register_setting( 'pehobr_app_options_group', 'start_date_setting', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '2026-02-18', ) );
}
add_action( 'admin_init', 'pehobr_register_settings' );

function pehobr_render_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Nastavení začátku doby postní</h1>
        <p>Toto nastavení určuje, od jakého data se začne den po dni zobrazovat obsah v aplikaci (Liturgické čtení, Pobožnosti, Denní kapky na úvodní stránce).</p>
        <form action="options.php" method="post">
            <?php settings_fields( 'pehobr_app_options_group' ); do_settings_sections( 'pehobr-app-settings' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"> <label for="start_date_setting">Datum začátku (Popeleční středa):</label> </th>
                    <td> <input type="date" id="start_date_setting" name="start_date_setting" value="<?php echo esc_attr( get_option( 'start_date_setting', '2026-02-18' ) ); ?>" /> <p class="description"> Zadejte datum ve formátu RRRR-MM-DD. </p> </td>
                </tr>
            </table>
            <?php submit_button( 'Uložit změny' ); ?>
        </form>
    </div>
    <?php
}