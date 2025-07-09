<?php
/**
 * Template Name: Postní Písně App
 * Description: Zobrazí seznam písní z kancionálu načtených z Google Sheet.
 *
 * @package minimalistblogger-child
 */

get_header();

function fetch_google_sheet_data_robust() {
    // URL adresa k vaší publikované Google Tabulce ve formátu CSV
    $sheet_url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQ-leW973M3f7lcPVH6YodDiwivVSn61BnKYSmC2IZXl6u_7adWM3N_TxQa1t6RCO2fZtxepjDVLSYm/pub?gid=0&single=true&output=csv';

    // Použijeme nový klíč pro mezipaměť, abychom zajistili načtení nových dat
    $transient_key = 'postni_pisne_data_v2';
    $songs_processed = get_transient($transient_key);

    if (false === $songs_processed) {
        $response = wp_remote_get($sheet_url, ['timeout' => 20]);
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return [];
        }

        $csv_data = wp_remote_retrieve_body($response);
        $lines = explode("\n", trim($csv_data));
        array_shift($lines); // Odstranění hlavičky

        $songs_processed = [];
        $current_song_index = -1;

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $row = str_getcsv($line);

            // Podmínka pro identifikaci hlavního řádku písně (má číslo a platnou URL u audia)
            $is_new_song_entry = isset($row[0]) && is_numeric(trim($row[0])) && isset($row[2]) && filter_var(trim($row[2]), FILTER_VALIDATE_URL);

            if ($is_new_song_entry) {
                $current_song_index++;
                $songs_processed[$current_song_index] = [
                    'cislo' => trim($row[0]),
                    'nazev' => trim($row[1] ?? ''),
                    'audio' => trim($row[2] ?? ''),
                    'noty'  => trim($row[3] ?? ''),
                    'text'  => trim($row[4] ?? '')
                ];
            } elseif ($current_song_index !== -1) {
                // Pokud to není nový řádek, připojíme text k předchozí písni
                $songs_processed[$current_song_index]['text'] .= "\n" . trim(implode(", ", $row));
            }
        }
        
        set_transient($transient_key, $songs_processed, HOUR_IN_SECONDS);
    }
    return $songs_processed;
}

$pisne = fetch_google_sheet_data_robust();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Postní písně z kancionálu</h1>
            </header>
            <div class="entry-content">
                <div class="songs-list-container">
                    <?php if (!empty($pisne)) : ?>
                        <?php foreach ($pisne as $pisen) : ?>
                            <?php 
                            $full_title = esc_html($pisen['cislo'] . ' - ' . $pisen['nazev']);
                            ?>
                            <button class="song-item-button" 
                                    data-title="<?php echo $full_title; ?>"
                                    data-audio="<?php echo esc_attr($pisen['audio']); ?>"
                                    data-image="<?php echo esc_attr($pisen['noty']); ?>"
                                    data-text="<?php echo esc_attr($pisen['text']); ?>">
                                <?php echo $full_title; ?>
                            </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Seznam písní se nepodařilo načíst. Zkuste prosím obnovit stránku.</p>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    </main>
</div>

<div id="song-modal-overlay" style="display: none;"></div>
<div id="song-modal-container" style="display: none;">
    <button id="song-modal-close-btn">×</button>
    <div id="song-modal-content">
        <h2 id="song-modal-title"></h2>
        <audio id="song-modal-audio" controls></audio>
        <div class="song-modal-image-wrapper">
            <img id="song-modal-image" src="" alt="Notový zápis">
        </div>
        <div id="song-modal-text"></div>
    </div>
</div>

<?php

get_sidebar(); // <-- PŘIDANÝ ŘÁDEK
get_footer();
?>