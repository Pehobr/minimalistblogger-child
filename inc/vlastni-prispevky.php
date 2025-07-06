<?php
/**
 * Registrace vlastních typů příspěvků (Custom Post Types) a taxonomií.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

function pehobr_register_daily_drops_cpt() {
    $labels = array( 'name' => _x( 'Denní kapky', 'Post Type General Name', 'minimalistblogger-child' ), 'singular_name' => _x( 'Denní kapka', 'Post Type Singular Name', 'minimalistblogger-child' ), 'menu_name' => __( 'Denní kapky', 'minimalistblogger-child' ), 'name_admin_bar' => __( 'Denní kapka', 'minimalistblogger-child' ), 'archives' => __( 'Archiv Denních kapek', 'minimalistblogger-child' ), 'attributes' => __( 'Atributy Denní kapky', 'minimalistblogger-child' ), 'parent_item_colon' => __( 'Rodičovská položka:', 'minimalistblogger-child' ), 'all_items' => __( 'Všechny Denní kapky', 'minimalistblogger-child' ), 'add_new_item' => __( 'Přidat novou Denní kapku', 'minimalistblogger-child' ), 'add_new' => __( 'Přidat novou', 'minimalistblogger-child' ), 'new_item' => __( 'Nová Denní kapka', 'minimalistblogger-child' ), 'edit_item' => __( 'Upravit Denní kapku', 'minimalistblogger-child' ), 'update_item' => __( 'Aktualizovat Denní kapku', 'minimalistblogger-child' ), 'view_item' => __( 'Zobrazit Denní kapku', 'minimalistblogger-child' ), 'view_items' => __( 'Zobrazit Denní kapky', 'minimalistblogger-child' ), 'search_items' => __( 'Hledat Denní kapku', 'minimalistblogger-child' ), );
    $args = array( 'label' => __( 'Denní kapka', 'minimalistblogger-child' ), 'description' => __( 'Obsah pro denní zobrazení na úvodní stránce.', 'minimalistblogger-child' ), 'labels' => $labels, 'supports' => array( 'title', 'editor', 'custom-fields' ), 'hierarchical' => false, 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'menu_position' => 5, 'menu_icon' => 'dashicons-calendar-alt', 'show_in_admin_bar' => true, 'show_in_nav_menus' => true, 'can_export' => true, 'has_archive' => false, 'exclude_from_search' => true, 'publicly_queryable' => true, 'capability_type' => 'post', 'show_in_rest' => true, );
    register_post_type( 'denni_kapka', $args );
}
add_action( 'init', 'pehobr_register_daily_drops_cpt', 0 );

function create_papez_taxonomy() {
    $labels = array( 'name' => _x( 'Papežové', 'taxonomy general name', 'minimalistblogger-child' ), 'singular_name' => _x( 'Papež', 'taxonomy singular name', 'minimalistblogger-child' ), 'search_items' => __( 'Hledat papeže', 'minimalistblogger-child' ), 'all_items' => __( 'Všichni papežové', 'minimalistblogger-child' ), 'parent_item' => __( 'Nadřazený papež', 'minimalistblogger-child' ), 'parent_item_colon' => __( 'Nadřazený papež:', 'minimalistblogger-child' ), 'edit_item' => __( 'Upravit papeže', 'minimalistblogger-child' ), 'update_item' => __( 'Aktualizovat papeže', 'minimalistblogger-child' ), 'add_new_item' => __( 'Přidat nového papeže', 'minimalistblogger-child' ), 'new_item_name' => __( 'Jméno nového papeže', 'minimalistblogger-child' ), 'menu_name' => __( 'Papežové', 'minimalistblogger-child' ), );
    $args = array( 'hierarchical' => true, 'labels' => $labels, 'show_ui' => true, 'show_admin_column' => true, 'query_var' => true, 'rewrite' => array( 'slug' => 'papez' ), 'show_in_rest' => true, );
    register_taxonomy( 'papez', array( 'denni_kapka' ), $args );
}
add_action( 'init', 'create_papez_taxonomy' );