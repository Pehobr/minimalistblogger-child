<?php
/**
 * Funkce pro stránku nastavení YouTube playlistů.
 * VERZE 3: Přidáno pole pro název playlistu a aktualizována migrace.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Registrace pole pro ukládání dat o playlistech.
 */
function pehobr_register_youtube_playlist_settings() {
    register_setting(
        'pehobr_youtube_playlists_group',
        'pehobr_youtube_playlists',
        'pehobr_sanitize_youtube_playlists'
    );
}
add_action( 'admin_init', 'pehobr_register_youtube_playlist_settings' );

/**
 * Jednorázová migrace původních, staticky zapsaných playlistů do nového systému nastavení.
 * Spustí se pouze jednou.
 */
function pehobr_migrate_static_playlists_to_settings() {
    if ( get_option( 'pehobr_youtube_migration_done' ) ) {
        return;
    }

    $daily_items = [
        ['title' => 'Televize NOE', 'image_url' => 'https://zpravy.proglas.cz/res/archive/280/062778.png?seek=1498474045', 'playlist_id' => 'PLQ0VblkXIA4wokyX7NZm7MvBdTRsWR8X6'],
        ['title' => 'P. Šebestián, OFM', 'image_url' => 'https://postnikapky.cz/wp-content/uploads/2023/01/maxresdefault-300x169.jpg', 'playlist_id' => 'UUQXkJsp9wBiSzNQ-JbEqMWw'],
    ];
    $weekly_items = [
        ['title' => 'Lomecká vigilie', 'image_url' => 'https://postnikapky.cz/wp-content/uploads/2022/02/Zachyceni_webu_5-2-2022_153124_www.lomec_.cz_-300x183.jpg', 'playlist_id' => 'PLmTG1ecR3a_QRajXuNldZweNx1UQtIJSJ'],
        ['title' => 'Dýchej Slovo', 'image_url' => 'https://postnikapky.cz/wp-content/uploads/2024/01/02301185-300x169.jpeg', 'playlist_id' => 'PLP2LVEgwOzCzHllFx8_SGMN343FE71LXe'],
        ['title' => 'P. Tomáš Halík', 'image_url' => 'https://postnikapky.cz/wp-content/uploads/2021/02/TomasHalik-300x160.png', 'playlist_id' => 'PLAtQGAGIuIYyYTvKYbI7YSu8QtIZ5xHFo'],
    ];

    $all_playlists = array_merge($daily_items, $weekly_items);
    $formatted_playlists = [];

    foreach($all_playlists as $playlist) {
        $formatted_playlists[] = [
            'title'       => $playlist['title'], // Přidáváme název
            'image_url'   => $playlist['image_url'],
            'playlist_id' => $playlist['playlist_id']
        ];
    }
    
    update_option('pehobr_youtube_playlists', $formatted_playlists);
    update_option( 'pehobr_youtube_migration_done', true );
}
add_action( 'admin_init', 'pehobr_migrate_static_playlists_to_settings' );

/**
 * Ošetří a zvaliduje data odeslaná z formuláře.
 */
function pehobr_sanitize_youtube_playlists($input) {
    $new_input = array();
    if ( isset( $input ) && is_array( $input ) ) {
        foreach ( $input as $key => $playlist ) {
            $new_playlist = array();
            $new_playlist['title'] = isset($playlist['title']) ? sanitize_text_field( trim($playlist['title']) ) : '';
            $new_playlist['image_url'] = isset($playlist['image_url']) ? esc_url_raw( trim($playlist['image_url']) ) : '';
            $new_playlist['playlist_id'] = isset($playlist['playlist_id']) ? sanitize_text_field( trim($playlist['playlist_id']) ) : '';

            if( !empty($new_playlist['playlist_id']) ) {
                 $new_input[] = $new_playlist;
            }
        }
    }
    return $new_input;
}

/**
 * Vykreslí HTML kód pro stránku s nastavením v administraci.
 */
function pehobr_render_youtube_settings_page() {
    ?>
    <div class="wrap">
        <h1>Nastavení YouTube Playlistů</h1>
        <p>Zde můžete spravovat seznam YouTube playlistů, které se zobrazují v aplikaci (např. na stránce "Video Kapky").</p>

        <form method="post" action="options.php">
            <?php
                settings_fields( 'pehobr_youtube_playlists_group' );
                $playlists = get_option( 'pehobr_youtube_playlists', array() );
            ?>

            <table class="wp-list-table widefat striped" id="youtube-playlist-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Název playlistu</th>
                        <th style="width: 35%;">URL obrázku</th>
                        <th style="width: 20%;">ID YouTube playlistu</th>
                        <th style="width: 10%;">Akce</th>
                    </tr>
                </thead>
                <tbody id="playlist-rows">
                    <?php if ( ! empty( $playlists ) ) : ?>
                        <?php foreach ( $playlists as $index => $playlist ) : ?>
                            <tr>
                                <td><input type="text" name="pehobr_youtube_playlists[<?php echo $index; ?>][title]" value="<?php echo esc_attr( $playlist['title'] ); ?>" class="large-text"></td>
                                <td><input type="url" name="pehobr_youtube_playlists[<?php echo $index; ?>][image_url]" value="<?php echo esc_attr( $playlist['image_url'] ); ?>" class="large-text"></td>
                                <td><input type="text" name="pehobr_youtube_playlists[<?php echo $index; ?>][playlist_id]" value="<?php echo esc_attr( $playlist['playlist_id'] ); ?>" class="large-text"></td>
                                <td><button type="button" class="button button-secondary remove-playlist-row">Odebrat</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <p style="margin-top: 20px;">
                <button type="button" id="add-playlist-row" class="button button-primary">Přidat nový playlist</button>
            </p>

            <?php submit_button('Uložit změny'); ?>
        </form>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var tableBody = $('#playlist-rows');
        $('#add-playlist-row').on('click', function() {
            var newIndex = tableBody.find('tr').length;
            var newRow = '<tr>' +
                '<td><input type="text" name="pehobr_youtube_playlists[' + newIndex + '][title]" class="large-text" placeholder="Např. Televize NOE"></td>' +
                '<td><input type="url" name="pehobr_youtube_playlists[' + newIndex + '][image_url]" class="large-text" placeholder="https://example.com/obrazek.jpg"></td>' +
                '<td><input type="text" name="pehobr_youtube_playlists[' + newIndex + '][playlist_id]" class="large-text" placeholder="PLxxxxxxxxxxxxxxxxx"></td>' +
                '<td><button type="button" class="button button-secondary remove-playlist-row">Odebrat</button></td>' +
                '</tr>';
            tableBody.append(newRow);
        });
        tableBody.on('click', '.remove-playlist-row', function() {
            $(this).closest('tr').remove();
        });
    });
    </script>
    <?php
}