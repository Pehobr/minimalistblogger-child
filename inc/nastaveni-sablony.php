<?php
/**
 * Základní nastavení a funkce šablony.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

function register_mobile_menu_location() {
    register_nav_menus(
        array(
            'mobile_extra_menu' => __( 'Pravé mobilní menu', 'minimalistblogger-child' ),
            'left_mobile_menu'  => __( 'Levé mobilní menu', 'minimalistblogger-child' ),
        )
    );
}
add_action( 'init', 'register_mobile_menu_location' );

function pehobr_add_liturgical_color_body_class($classes) {
    $config_path = get_stylesheet_directory() . '/zmena-liturgicke-barvy.php';
    if ( file_exists($config_path) ) {
        $liturgicky_kalendar = include $config_path;
    } else {
        $liturgicky_kalendar = array();
    }

    $timezone = new DateTimeZone('Europe/Prague');
    $dnesni_datum_obj = new DateTime('now', $timezone);

    $trvala_zmena_od_data = new DateTime('2026-04-06', $timezone);
    $barva_po_zmene = 'bezova';
    $vychozi_barva = 'fialova';
    $barva_dnes = $vychozi_barva;

    if ($dnesni_datum_obj >= $trvala_zmena_od_data) {
        $barva_dnes = $barva_po_zmene;
    } else {
        $dnesni_datum_format = $dnesni_datum_obj->format('Y-m-d');
        if (isset($liturgicky_kalendar[$dnesni_datum_format])) {
            $barva_dnes = $liturgicky_kalendar[$dnesni_datum_format];
        }
    }

    $classes[] = 'theme-' . $barva_dnes;
    return $classes;
}
add_filter('body_class', 'pehobr_add_liturgical_color_body_class');