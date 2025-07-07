<?php
/**
 * Funkce pro administraci WordPressu.
 * VERZE 4: Hlavní menu "Postní kapky" odkazuje přímo na nastavení data.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Registruje stránky s nastavením.
 */
function pehobr_register_settings_page() {
    // Vytvoření hlavní položky menu "Postní kapky",
    // která přímo odkazuje na stránku "Nastavení data".
    add_menu_page(
        'Nastavení začátku doby postní', // Titulek stránky v prohlížeči
        'Nastavení PK', // Název v menu
        'manage_options', // Oprávnění
        'pehobr-app-settings', // Slug, který je stejný jako u první podpoložky
        'pehobr_render_settings_page_content', // Funkce pro vykreslení obsahu
        'dashicons-admin-generic', // Ikona
        20 // Pozice
    );

    // Přidání podstránky pro nastavení YouTube playlistů
    add_submenu_page(
        'pehobr-app-settings', // Slug rodičovského menu (stejný jako u add_menu_page)
        'Nastavení Youtube playlistů', // Titulek stránky
        'Youtube playlisty', // Název v podmenu
        'manage_options', // Oprávnění
        'pehobr-youtube-settings', // Slug této podstránky
        'pehobr_render_youtube_settings_page' // Funkce pro vykreslení obsahu
    );

    // === PŘIDANÁ SEKCE PRO INTERNETOVÁ RÁDIA ===
    add_submenu_page(
        'pehobr-app-settings', // Slug rodičovského menu
        'Nastavení internetových rádií', // Titulek stránky
        'Internetová rádia', // Název v podmenu
        'manage_options', // Oprávnění
        'pehobr-radio-settings', // Slug této podstránky
        'pehobr_render_radio_settings_page' // Funkce pro vykreslení obsahu
    );
    // === KONEC PŘIDANÉ SEKCE ===
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