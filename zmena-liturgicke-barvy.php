<?php
/**
 * Konfigurace liturgických barev.
 *
 * Tento soubor obsahuje pouze pole s mapováním datumů na názvy barev.
 * Slouží pro snadnou úpravu v dalších letech.
 *
 * @package minimalistblogger-child
 */

// Zajistíme, aby soubor nebyl přístupný přímo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
    
    // --- ZDE DEFINUJTE MAPOVÁNÍ DATUMŮ NA BARVY ---
    // Formát je 'YYYY-MM-DD' => 'nazev-barvy'.
    
    // NASTAVENÍ PRO ROK 2026
    '2026-03-15' => 'ruzova',   // 4. neděle postní (Laetare)
    '2026-03-19' => 'bezova',   // sv. Josef
    '2026-03-25' => 'modra',    // Zvěstování Páně
    '2026-03-29' => 'cervena',  // Květná neděle
    '2026-04-02' => 'bezova',   // Zelený čtvrtek
    '2026-04-03' => 'cervena',  // Velký pátek
    '2026-04-05' => 'bezova',   // Vzkříšení Páně
    
    // Sem můžete případně doplnit další specifická data před 6. 4. 2026

);