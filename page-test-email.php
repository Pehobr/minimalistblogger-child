<?php
/**
 * Template Name: Testovací náhled e-mailu
 * Description: Zobrazí HTML šablonu e-mailu s testovacími daty.
 */

// --- Zde jsou testovací data pro náhled ---
$data = [
    'jpii_quote'      => 'Láska mi všechno vysvětlila, láska všechno vyřešila – proto lásku obdivuji, ať se nachází kdekoli.',
    'benedict_quote'  => 'Svět nabízí pohodlí, ale vy jste nebyli stvořeni pro pohodlí. Byli jste stvořeni pro velikost.',
    'francis_quote'   => 'Řeka se nevrací ke svému prameni, přesto musí na pramen pamatovat. Člověk musí jít dál. A pokud ztratí paměť na své kořeny a smysl pro dějiny, pak je zničen.',
    'leo_quote'       => 'Každý člověk je od přírody povolán k tomu, aby se staral o své vlastní zájmy, a je spravedlivé, aby si ponechal přiměřenou část toho, co vyprodukuje, aby si zajistil své živobytí.',
    'augustine_quote' => 'Bůh miluje každého z nás, jako by existoval jen jeden z nás.',
    'prayer'          => 'Pane, dej mi sílu změnit věci, které změnit mohu, dej mi trpělivost snášet věci, které změnit nemohu, a dej mi moudrost, abych je od sebe dokázal rozlišit.',
    'image_url'       => 'http://pkapky2026.local/wp-content/uploads/2025/06/what-ive-learned-from-road-trips-1.png',
    'subject'         => '[NÁHLED] Denní postní kapka',
    'web_view_url'    => '#'
];

// Načtení samotné HTML šablony e-mailu
$template_path = get_stylesheet_directory() . '/email-template.php';

if (file_exists($template_path)) {
    // Zpřístupní proměnné z pole $data pro šablonu
    extract($data);
    // Vloží a zobrazí HTML kód šablony
    include($template_path);
} else {
    // Pokud šablona neexistuje, zobrazí se chybová hláška
    echo '<h1>Chyba: Soubor šablony e-mailu nebyl nalezen!</h1>';
    echo '<p>Ujistěte se, že soubor <code>email-template.php</code> existuje ve složce vaší šablony.</p>';
}
?>