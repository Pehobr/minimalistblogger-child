<?php

// NAČTENÍ STYLŮ A SKRIPTŮ
function minimalistblogger_child_enqueue_styles() {
    wp_enqueue_style( 'minimalistblogger-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'minimalistblogger-child-style', get_stylesheet_directory_uri() . '/style.css', array('minimalistblogger-parent-style'));
    wp_enqueue_style( 'rozlozeni', get_stylesheet_directory_uri() .'/css/rozlozeni.css', array(), filemtime(get_stylesheet_directory() .'/css/rozlozeni.css'));
    wp_enqueue_style( 'vzhled-mobil', get_stylesheet_directory_uri() .'/css/vzhled-mobil.css', array(), filemtime(get_stylesheet_directory() .'/css/vzhled-mobil.css'));
    wp_enqueue_style( 'vzhled-pc', get_stylesheet_directory_uri() .'/css/vzhled-pc.css', array(), filemtime(get_stylesheet_directory() .'/css/vzhled-pc.css'));
    wp_enqueue_style( 'fixni-hlavicka', get_stylesheet_directory_uri() .'/css/fixni-hlavicka.css', array(), filemtime(get_stylesheet_directory() .'/css/fixni-hlavicka.css'));
    wp_enqueue_style( 'dolni-lista', get_stylesheet_directory_uri() .'/css/dolni-lista.css', array(), filemtime(get_stylesheet_directory() .'/css/dolni-lista.css'));
    wp_enqueue_style( 'mobile-menu', get_stylesheet_directory_uri() .'/css/mobile-menu.css', array(), filemtime(get_stylesheet_directory() .'/css/mobile-menu.css'));
    wp_enqueue_style( 'nastaveni-panel', get_stylesheet_directory_uri() .'/css/nastaveni-panel.css', array(), filemtime(get_stylesheet_directory() .'/css/nastaveni-panel.css'));
    wp_enqueue_style( 'prispevky', get_stylesheet_directory_uri() .'/css/prispevky.css', array(), filemtime(get_stylesheet_directory() .'/css/prispevky.css'));

    if ( is_page_template( 'page-home.php' ) ) {
        wp_enqueue_style( 'page-home-style', get_stylesheet_directory_uri() .'/css/page-home.css', array(), filemtime(get_stylesheet_directory() .'/css/page-home.css') );
        wp_enqueue_script( 'page-home-js', get_stylesheet_directory_uri() . '/js/page-home.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/page-home.js'), true );
    }

    if ( is_page_template( 'page-liturgicke-cteni.php' ) ) {
        wp_enqueue_style( 'liturgicke-cteni', get_stylesheet_directory_uri() .'/css/liturgicke-cteni.css', array(), filemtime(get_stylesheet_directory() .'/css/liturgicke-cteni.css'));
        wp_enqueue_script( 'liturgicke-cteni-js', get_stylesheet_directory_uri() . '/js/liturgicke-cteni.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/liturgicke-cteni.js'), true );
    }

    if ( is_page_template( 'page-poboznosti.php' ) ) {
        wp_enqueue_style( 'poboznosti', get_stylesheet_directory_uri() .'/css/poboznosti.css', array(), filemtime(get_stylesheet_directory() .'/css/poboznosti.css'));
        wp_enqueue_script( 'poboznosti-js', get_stylesheet_directory_uri() . '/js/poboznosti.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/poboznosti.js'), true );
    }

    if ( is_page_template( 'page-archiv-citatu.php' ) ) {
        wp_enqueue_style( 'archiv-citatu', get_stylesheet_directory_uri() .'/css/archiv-citatu.css', array(), filemtime(get_stylesheet_directory() .'/css/archiv-citatu.css'));
        wp_enqueue_script( 'archiv-citatu-js', get_stylesheet_directory_uri() . '/js/archiv-citatu.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/archiv-citatu.js'), true );
    }

     if ( is_page_template( 'page-radio.php' ) ) {
        wp_enqueue_style( 'page-radia', get_stylesheet_directory_uri() .'/css/page-radia.css', array(), filemtime(get_stylesheet_directory() .'/css/page-radia.css'));
        wp_enqueue_script( 'page-radia-js', get_stylesheet_directory_uri() . '/js/page-radia.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/page-radia.js'), true );
    }

    if ( is_page_template( 'page-oblibene.php' ) ) {
        wp_enqueue_style( 'oblibene-style', get_stylesheet_directory_uri() .'/css/oblibene.css', array(), filemtime(get_stylesheet_directory() .'/css/oblibene.css') );
        wp_enqueue_script( 'oblibene-js', get_stylesheet_directory_uri() . '/js/oblibene.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/oblibene.js'), true );
    }

     if ( is_page_template( 'page-zapisnik.php' ) ) {
        wp_enqueue_style( 'zapisnik-style', get_stylesheet_directory_uri() .'/css/zapisnik.css', array(), filemtime(get_stylesheet_directory() .'/css/zapisnik.css') );
        wp_enqueue_script( 'zapisnik-js', get_stylesheet_directory_uri() . '/js/zapisnik.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/zapisnik.js'), true );
    }

    if ( is_page_template( 'page-navody.php' ) ) {
        wp_enqueue_style( 'navody-style', get_stylesheet_directory_uri() .'/css/navody.css', array(), filemtime(get_stylesheet_directory() .'/css/navody.css') );
    }

    wp_enqueue_script( 'dolni-lista-js', get_stylesheet_directory_uri() . '/js/dolni-lista.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/dolni-lista.js'), true );
    wp_enqueue_script( 'mobile-menu-js', get_stylesheet_directory_uri() . '/js/mobile-menu.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/mobile-menu.js'), true );
    wp_enqueue_script( 'nastaveni-panel-js', get_stylesheet_directory_uri() . '/js/nastaveni-panel.js', array( 'jquery' ), filemtime(get_stylesheet_directory() .'/js/nastaveni-panel.js'), true );
}
add_action( 'wp_enqueue_scripts', 'minimalistblogger_child_enqueue_styles' );

// ZMĚNA LITURGICKÉ BARVY
function zmena_liturgicke_barvy() {
    $file_path = get_stylesheet_directory() . '/zmena-liturgicke-barvy.php';
    if ( file_exists( $file_path ) ) {
        include( $file_path );
    }
}
add_action( 'wp_head', 'zmena_liturgicke_barvy' );

// PŘIDÁNÍ TEXTU DO ZÁPATÍ
function pridat_text_do_zapati() {
    echo '<p>Text přidaný do zápatí.</p>';
}
add_action( 'wp_footer', 'pridat_text_do_zapati' );


// =========================================================================
// REGISTRACE CUSTOM POST TYPE PRO EMAIL KAPKY
// =========================================================================

function create_email_kapky_cpt() {
    $labels = array(
        'name'                  => _x( 'Email kapky', 'Post Type General Name', 'minimalistblogger-child' ),
        'singular_name'         => _x( 'Email kapka', 'Post Type Singular Name', 'minimalistblogger-child' ),
        'menu_name'             => __( 'Email kapky', 'minimalistblogger-child' ),
        'all_items'             => __( 'Všechny kapky', 'minimalistblogger-child' ),
        'add_new_item'          => __( 'Přidat novou kapku', 'minimalistblogger-child' ),
    );
    $args = array(
        'label'                 => __( 'Email kapka', 'minimalistblogger-child' ),
        'description'           => __( 'Denní obsah pro e-mailové kapky', 'minimalistblogger-child' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-email-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type( 'email_kapky', $args );
}
add_action( 'init', 'create_email_kapky_cpt', 0 );


// =========================================================================
// PŘIDÁNÍ VLASTNÍCH POLÍ (META BOX) PRO EMAIL KAPKY
// =========================================================================

function email_kapky_add_meta_box() {
    add_meta_box('email_kapky_content', 'Obsah kapky', 'email_kapky_meta_box_callback', 'email_kapky', 'normal', 'high');
}
add_action( 'add_meta_boxes', 'email_kapky_add_meta_box' );

function email_kapky_meta_box_callback( $post ) {
    wp_nonce_field( 'email_kapky_save_meta_box_data', 'email_kapky_meta_box_nonce' );
    $citat_1 = get_post_meta( $post->ID, '_citat_1', true );
    $autor_1 = get_post_meta( $post->ID, '_autor_1', true );
    $citat_2 = get_post_meta( $post->ID, '_citat_2', true );
    $autor_2 = get_post_meta( $post->ID, '_autor_2', true );
    echo '<style> .kapka-field { display: flex; flex-direction: column; margin-bottom: 15px; } .kapka-field label { font-weight: bold; margin-bottom: 5px; } .kapka-field input, .kapka-field textarea { width: 100%; } </style>';
    echo '<div class="kapka-field"><label for="citat_1_field">Citát 1</label><textarea id="citat_1_field" name="citat_1_field" rows="3">' . esc_attr( $citat_1 ) . '</textarea></div>';
    echo '<div class="kapka-field"><label for="autor_1_field">Autor citátu 1</label><input type="text" id="autor_1_field" name="autor_1_field" value="' . esc_attr( $autor_1 ) . '" /></div>';
    echo '<hr style="margin: 20px 0;">';
    echo '<div class="kapka-field"><label for="citat_2_field">Citát 2</label><textarea id="citat_2_field" name="citat_2_field" rows="3">' . esc_attr( $citat_2 ) . '</textarea></div>';
    echo '<div class="kapka-field"><label for="autor_2_field">Autor citátu 2</label><input type="text" id="autor_2_field" name="autor_2_field" value="' . esc_attr( $autor_2 ) . '" /></div>';
}

function email_kapky_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['email_kapky_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['email_kapky_meta_box_nonce'], 'email_kapky_save_meta_box_data' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( isset( $_POST['post_type'] ) && 'email_kapky' == $_POST['post_type'] && ! current_user_can( 'edit_post', $post_id ) ) return;
    $fields = ['citat_1', 'autor_1', 'citat_2', 'autor_2'];
    foreach ($fields as $field) {
        if ( isset( $_POST[$field . '_field'] ) ) {
            if ($field === 'citat_1' || $field === 'citat_2') {
                update_post_meta($post_id, '_' . $field, sanitize_textarea_field($_POST[$field . '_field']));
            } else {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field . '_field']));
            }
        }
    }
}
add_action( 'save_post', 'email_kapky_save_meta_box_data' );

// =========================================================================
// AUTOMATICKÉ ODESÍLÁNÍ EMAIL KAPEK PŘES ECOMAIL (DEFINITIVNĚ FINÁLNÍ VERZE)
// =========================================================================

require_once( get_stylesheet_directory() . '/email-kapky.php' );

function send_daily_email_kapka() {

    date_default_timezone_set('Europe/Prague');
    $target_date = date('Y-m-d', strtotime('+1 day'));
    $html_content = generate_daily_email_content($target_date);

    if (!$html_content) {
        error_log('Kapka pro ' . $target_date . ' nenalezena. E-mail se neodesílá.');
        return;
    }

    $api_key = 'aa5b20ef43134327157d059786b0e402b540c89792374ef039ff025f2e525923';
    $list_id = 1;

    // KROK 1: Vytvoření kampaně
    $create_campaign_url = 'https://api2.ecomailapp.cz/campaigns';
    $campaign_name = 'Postní kapka - ' . $target_date;
    $subject = 'Postní kapka na den: ' . date('j. n. Y', strtotime($target_date));

    // FINÁLNÍ OPRAVA: Použit správný název parametru 'recepient_lists'
    $campaign_data = array(
        'name' => $campaign_name,
        'title' => $subject,
        'subject' => $subject,
        'recepient_lists' => [ $list_id ], // Správný název (e místo i) a formát pole
        'from_name' => 'Postní kapky',
        'from_email' => 'info@mail.postnikapky.cz',
        'reply_to' => 'favnorovy@ado.cz',
        'html_content' => $html_content,
    );

    $create_args = array(
        'method' => 'POST',
        'headers' => array(
            'Content-Type' => 'application/json',
            'key' => $api_key,
        ),
        'body' => json_encode($campaign_data),
        'timeout' => 30,
    );

    $create_response = wp_remote_post($create_campaign_url, $create_args);

    if (is_wp_error($create_response) || wp_remote_retrieve_response_code($create_response) != 200) {
        error_log('Chyba při vytváření kampaně v Ecomailu: ' . print_r($create_response, true));
        return $create_response;
    }

    $created_campaign = json_decode(wp_remote_retrieve_body($create_response), true);
    $campaign_id = isset($created_campaign['id']) ? $created_campaign['id'] : null;

    if (!$campaign_id) {
        error_log('Nepodařilo se získat ID nově vytvořené kampaně.');
        return $create_response;
    }

    // KROK 2: Odeslání nově vytvořené kampaně
    $send_campaign_url = "https://api2.ecomailapp.cz/campaign/{$campaign_id}/send";
    
    $send_args = array(
        'method' => 'GET',
        'headers' => array(
            'key' => $api_key,
        ),
        'timeout' => 30,
    );

    $send_response = wp_remote_get($send_campaign_url, $send_args);
    
    return $send_response;
}
add_action('send_daily_kapka_hook', 'send_daily_email_kapka');


// Plánování a manuální spuštění zůstává stejné
function schedule_daily_kapka_event() {
    if ( ! wp_next_scheduled( 'send_daily_kapka_hook' ) ) {
        wp_schedule_event( strtotime('tomorrow 2:00am'), 'daily', 'send_daily_kapka_hook' );
    }
}
add_action( 'after_switch_theme', 'schedule_daily_kapka_event' );

function unschedule_daily_kapka_event() {
    wp_clear_scheduled_hook( 'send_daily_kapka_hook' );
}
add_action( 'switch_theme', 'unschedule_daily_kapka_event' );

function manual_email_kapka_trigger() {
    if ( isset( $_GET['test_ecomail'] ) && $_GET['test_ecomail'] === 'odeslat' ) {
        if ( current_user_can( 'manage_options' ) ) {
            echo "<h1>Spouštím testovací odeslání...</h1>";
            $response = send_daily_email_kapka();
            echo "<h2>Odpověď od Ecomail API:</h2>";
            echo "<pre>";
            print_r($response);
            echo "</pre>";
            $response_code = wp_remote_retrieve_response_code( $response );
            $response_body = wp_remote_retrieve_body( $response );
            echo "<h3>Stavový kód: " . esc_html($response_code) . "</h3>";
            echo "<h3>Tělo odpovědi:</h3>";
            echo "<code>" . esc_html($response_body) . "</code>";
            exit;
        } else {
            wp_die('Pro tuto akci nemáte oprávnění.');
        }
    }
}
add_action( 'init', 'manual_email_kapka_trigger' );