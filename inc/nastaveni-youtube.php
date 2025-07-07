<?php
/**
 * Funkce pro stránku nastavení YouTube playlistů.
 * VERZE 4: Přidáno pole pro kategorii.
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
 * Ošetří a zvaliduje data odeslaná z formuláře.
 */
function pehobr_sanitize_youtube_playlists($input) {
    $new_input = array();
    if ( isset( $input ) && is_array( $input ) ) {
        foreach ( $input as $key => $playlist ) {
            // Přeskočíme prázdné řádky, které nemají ID playlistu
            if ( empty($playlist['playlist_id']) ) continue;

            $new_playlist = array();
            $new_playlist['title'] = isset($playlist['title']) ? sanitize_text_field( trim($playlist['title']) ) : '';
            $new_playlist['image_url'] = isset($playlist['image_url']) ? esc_url_raw( trim($playlist['image_url']) ) : '';
            $new_playlist['playlist_id'] = isset($playlist['playlist_id']) ? sanitize_text_field( trim($playlist['playlist_id']) ) : '';
            // Uložení nové kategorie
            $new_playlist['category'] = isset($playlist['category']) ? sanitize_text_field( trim($playlist['category']) ) : '';

            $new_input[] = $new_playlist;
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
        <p>Zde můžete spravovat seznam YouTube playlistů, které se zobrazují v aplikaci (např. na stránce "Video Kapky"). Můžete je také rozdělit do kategorií.</p>

        <form method="post" action="options.php">
            <?php
                settings_fields( 'pehobr_youtube_playlists_group' );
                $playlists = get_option( 'pehobr_youtube_playlists', array() );
            ?>

            <table class="wp-list-table widefat striped" id="youtube-playlist-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Název playlistu</th>
                        <th style="width: 30%;">URL obrázku</th>
                        <th style="width: 20%;">ID YouTube playlistu</th>
                        <th style="width: 15%;">Kategorie</th>
                        <th style="width: 10%;">Akce</th>
                    </tr>
                </thead>
                <tbody id="playlist-rows">
                    <?php if ( ! empty( $playlists ) ) : ?>
                        <?php foreach ( $playlists as $index => $playlist ) : ?>
                            <tr>
                                <td><input type="text" name="pehobr_youtube_playlists[<?php echo $index; ?>][title]" value="<?php echo esc_attr( $playlist['title'] ?? '' ); ?>" class="large-text"></td>
                                <td><input type="url" name="pehobr_youtube_playlists[<?php echo $index; ?>][image_url]" value="<?php echo esc_attr( $playlist['image_url'] ?? '' ); ?>" class="large-text"></td>
                                <td><input type="text" name="pehobr_youtube_playlists[<?php echo $index; ?>][playlist_id]" value="<?php echo esc_attr( $playlist['playlist_id'] ?? '' ); ?>" class="large-text"></td>
                                <td><input type="text" name="pehobr_youtube_playlists[<?php echo $index; ?>][category]" value="<?php echo esc_attr( $playlist['category'] ?? '' ); ?>" class="large-text" placeholder="Např. Denní promluvy"></td>
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
        
        function reindexRows() {
            tableBody.find('tr').each(function(index) {
                $(this).find('input').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        $('#add-playlist-row').on('click', function() {
            var newIndex = tableBody.find('tr').length;
            var newRow = '<tr>' +
                '<td><input type="text" name="pehobr_youtube_playlists[' + newIndex + '][title]" class="large-text" placeholder="Např. Televize NOE"></td>' +
                '<td><input type="url" name="pehobr_youtube_playlists[' + newIndex + '][image_url]" class="large-text" placeholder="https://example.com/obrazek.jpg"></td>' +
                '<td><input type="text" name="pehobr_youtube_playlists[' + newIndex + '][playlist_id]" class="large-text" placeholder="PLxxxxxxxxxxxxxxxxx"></td>' +
                '<td><input type="text" name="pehobr_youtube_playlists[' + newIndex + '][category]" class="large-text" placeholder="Např. Denní promluvy"></td>' +
                '<td><button type="button" class="button button-secondary remove-playlist-row">Odebrat</button></td>' +
                '</tr>';
            tableBody.append(newRow);
        });

        tableBody.on('click', '.remove-playlist-row', function() {
            $(this).closest('tr').remove();
            reindexRows();
        });
    });
    </script>
    <?php
}