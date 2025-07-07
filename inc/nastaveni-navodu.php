<?php
/**
 * Vytvoří stránku v administraci pro snadné řazení návodů (podstránek).
 * VERZE 2 - Opraveno načítání skriptů a název menu.
 */

// Zabráníme přímému přístupu
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Přidání podpoložky do menu "Postní kapky"
add_action('admin_menu', function() {
    add_submenu_page(
        'pehobr-app-settings',
        'Pořadí návodů',           // Titulek stránky v prohlížeči
        'Pořadí návodů',           // <<< OPRAVENO: Název v menu
        'manage_options',
        'nastaveni-poradi-navodu',
        'pehobr_render_sort_navody_page'
    );
});

// 2. Funkce, která vykreslí obsah stránky pro řazení
function pehobr_render_sort_navody_page() {
    $navody_parent_page_query = new WP_Query([
        'post_type' => 'page',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'no_found_rows' => true,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-navody.php'
    ]);
    $parent_page_id = !empty($navody_parent_page_query->posts) ? $navody_parent_page_query->posts[0] : 0;

    ?>
    <div class="wrap">
        <h1>Nastavení pořadí návodů</h1>
        <p>Přetáhněte položky myší a změňte tak jejich pořadí. Změny se ukládají automaticky.</p>

        <?php
        if ( $parent_page_id > 0 ) {
            $navody_query = new WP_Query([
                'post_type' => 'page',
                'posts_per_page' => -1,
                'post_parent' => $parent_page_id,
                'orderby' => 'menu_order',
                'order' => 'ASC',
            ]);

            if ($navody_query->have_posts()) : ?>
                <ul id="sortable-navody">
                    <?php while ($navody_query->have_posts()) : $navody_query->the_post(); ?>
                        <li id="post-<?php the_ID(); ?>" class="ui-state-default">
                            <span class="dashicons dashicons-menu"></span>
                            <?php the_title(); ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <div id="reorder-feedback" style="display:none; margin-top: 15px;"></div>
                <?php wp_reset_postdata();
            else :
                echo '<p>Nebyly nalezeny žádné podstránky (návody). Můžete je vytvořit v sekci <a href="' . admin_url('edit.php?post_type=page') . '">Stránky</a> a přiřadit jim rodičovskou stránku "Návody".</p>';
            endif;
        } else {
            echo '<p>Chyba: Nepodařilo se najít hlavní stránku "Návody", která používá šablonu <code>page-navody.php</code>.</p>';
        }
        ?>

        <style>
            #sortable-navody { list-style-type: none; margin: 20px 0; padding: 0; max-width: 600px; }
            #sortable-navody li { margin: 0 0 5px 0; padding: 10px 15px; font-size: 16px; background-color: #fff; border: 1px solid #ccc; cursor: move; display: flex; align-items: center; border-radius: 4px; }
            #sortable-navody li .dashicons { margin-right: 10px; color: #888; }
            .ui-state-highlight { height: 44px; background-color: #f0f0f0; border: 1px dashed #ccc; margin: 0 0 5px 0; }
        </style>
    </div>
    <?php
}

// 3. Načtení skriptů pro drag&drop (s kontrolou existence souboru)
add_action('admin_enqueue_scripts', function($hook) {
if ($hook != 'nastaveni-pk_page_nastaveni-poradi-navodu') {        return;
    }

    wp_enqueue_script('jquery-ui-sortable');

    // <<< OPRAVENO: Bezpečnější načtení skriptu
    $script_path = get_stylesheet_directory() . '/js/admin-reorder.js';
    $script_uri = get_stylesheet_directory_uri() . '/js/admin-reorder.js';

    if ( file_exists( $script_path ) ) {
        wp_enqueue_script(
            'pehobr-admin-reorder',
            $script_uri,
            ['jquery', 'jquery-ui-sortable'],
            filemtime($script_path), // Zabrání problémům s cache
            true
        );

        wp_localize_script('pehobr-admin-reorder', 'pehobr_reorder_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pehobr_reorder_nonce')
        ]);
    }
});

// 4. AJAX handler pro uložení nového pořadí
add_action('wp_ajax_pehobr_save_page_order', function() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pehobr_reorder_nonce' ) ) {
        wp_send_json_error( 'Chyba zabezpečení.', 403 );
        return;
    }

    if (current_user_can('edit_pages') && isset($_POST['order']) && is_array($_POST['order'])) {
        $order = $_POST['order'];
        foreach ($order as $index => $post_id_str) {
            $post_id = intval(str_replace('post-', '', $post_id_str));
            if ( $post_id > 0 ) {
                wp_update_post([
                    'ID' => $post_id,
                    'menu_order' => $index,
                ]);
            }
        }
        wp_send_json_success('Pořadí bylo úspěšně uloženo.');
    } else {
        wp_send_json_error('Nedostatečná oprávnění nebo neplatná data.');
    }
});