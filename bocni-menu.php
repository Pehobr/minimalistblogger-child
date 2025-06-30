<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Registers a new menu location for a custom side mobile menu.
 *
 * This function hooks into the 'init' action to register a new navigation menu
 * location in WordPress. The new location is named 'bocni_mobilni_menu_lokace'
 * and is intended for a custom mobile side menu.
 */
function moje_vlastni_menu_pozice_app() {
    register_nav_menus(
        array(
            'bocni_mobilni_menu_lokace' => __('Boční Mobilní Menu Lokace', 'textdomain_papezlev'),
        )
    );
}
add_action('init', 'moje_vlastni_menu_pozice_app');

/**
 * Displays the HTML structure for the custom side menu.
 *
 * This function generates the HTML markup for the custom side menu, including
 * a header with a title and a close button, and a container for the menu items.
 * It checks if a menu is assigned to the 'bocni_mobilni_menu_lokace' location
 * and displays it. If no menu is assigned, it shows a fallback message.
 * The menu is rendered as an unordered list.
 *
 * The function is designed to be called directly from a template file (e.g., header.php).
 */
function zobraz_vlastni_bocni_menu_strukturu() {
    ?>
    <div id="custom-side-menu" aria-hidden="true">
        <div class="custom-menu-header">
            <span class="custom-menu-title">Menu aplikace</span>
            <button id="custom-menu-close-btn" aria-label="Zavřít menu">&times;</button>
        </div>
        <div class="custom-menu-ul-container">
            <?php
            if (has_nav_menu('bocni_mobilni_menu_lokace')) {
                wp_nav_menu(array(
                    'theme_location' => 'bocni_mobilni_menu_lokace',
                    'container'      => false,
                    'menu_class'     => 'custom-menu-ul',
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth'          => 1
                ));
            } else {
                // Fallback message if menu is not set
                echo '<ul class="custom-menu-ul"><li><a href="#">Menu není nastaveno</a></li></ul>';
                if (current_user_can('edit_theme_options')) { // Show only to administrators
                    echo '<p style="padding:10px; text-align:center; font-size:12px; color:#555;">Nastavte prosím menu v Vzhled > Menu a přiřaďte ho k pozici "Boční Mobilní Menu Lokace".</p>';
                }
            }
            ?>
        </div>
    </div>
    <?php
}