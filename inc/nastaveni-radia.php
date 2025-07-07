<?php
/**
 * Funkce pro stránku nastavení internetových rádií.
 */

// Zabráníme přímému přístupu
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Registrace pole pro ukládání dat o rádiích.
 */
function pehobr_register_radio_settings() {
    register_setting(
        'pehobr_radio_settings_group',
        'pehobr_internet_radia',
        'pehobr_sanitize_radio_settings'
    );
}
add_action( 'admin_init', 'pehobr_register_radio_settings' );

/**
 * Ošetří a zvaliduje data odeslaná z formuláře.
 */
function pehobr_sanitize_radio_settings($input) {
    $new_input = array();
    if ( isset( $input ) && is_array( $input ) ) {
        // Použijeme array_values pro přeindexování pole, aby bylo pořadí správné
        $input = array_values($input);

        foreach ( $input as $key => $radio ) {
            // Přeskočíme prázdné řádky, které nemají název nebo URL streamu
            if ( empty($radio['nazev']) || empty($radio['stream_url']) ) continue;

            $new_radio = array();
            $new_radio['nazev'] = sanitize_text_field( trim($radio['nazev']) );
            $new_radio['stream_url'] = esc_url_raw( trim($radio['stream_url']) );
            $new_radio['image_url'] = isset($radio['image_url']) ? esc_url_raw( trim($radio['image_url']) ) : '';

            $new_input[] = $new_radio;
        }
    }
    return $new_input;
}

/**
 * Vykreslí HTML kód pro stránku s nastavením v administraci.
 */
function pehobr_render_radio_settings_page() {
    ?>
    <div class="wrap">
        <h1>Nastavení internetových rádií</h1>
        <p>Zde můžete spravovat seznam internetových rádií. Pořadí rádií můžete měnit přetažením řádků.</p>

        <form method="post" action="options.php">
            <?php
                settings_fields( 'pehobr_radio_settings_group' );
                $radia = get_option( 'pehobr_internet_radia', array() );
            ?>

            <table class="wp-list-table widefat striped" id="internet-radio-table">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="sort-handle-col">Pořadí</th>
                        <th style="width: 25%;">Název rádia</th>
                        <th style="width: 35%;">URL streamu</th>
                        <th style="width: 25%;">URL obrázku</th>
                        <th style="width: 10%;">Akce</th>
                    </tr>
                </thead>
                <tbody id="radio-rows">
                    <?php if ( ! empty( $radia ) ) : ?>
                        <?php foreach ( $radia as $index => $radio ) : ?>
                            <tr class="radio-row">
                                <td class="sort-handle"><span class="dashicons dashicons-menu"></span></td>
                                <td><input type="text" name="pehobr_internet_radia[<?php echo $index; ?>][nazev]" value="<?php echo esc_attr( $radio['nazev'] ?? '' ); ?>" class="large-text" required></td>
                                <td><input type="url" name="pehobr_internet_radia[<?php echo $index; ?>][stream_url]" value="<?php echo esc_attr( $radio['stream_url'] ?? '' ); ?>" class="large-text" required></td>
                                <td><input type="url" name="pehobr_internet_radia[<?php echo $index; ?>][image_url]" value="<?php echo esc_attr( $radio['image_url'] ?? '' ); ?>" class="large-text"></td>
                                <td><button type="button" class="button button-secondary remove-radio-row">Odebrat</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <p style="margin-top: 20px;">
                <button type="button" id="add-radio-row" class="button button-primary">Přidat nové rádio</button>
            </p>

            <?php submit_button('Uložit změny'); ?>
        </form>
    </div>

    <style>
        .sort-handle-col { text-align: center; }
        .sort-handle { cursor: move; text-align: center; vertical-align: middle !important; }
        .radio-row.ui-sortable-helper { background-color: #f9f9f9; border: 1px dashed #ccc; }
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Ujistíme se, že je načteno jQuery UI Sortable
        if (typeof $.fn.sortable === 'undefined') {
            console.error("jQuery UI Sortable is not loaded.");
            return;
        }

        var tableBody = $('#radio-rows');

        // Funkce pro přeindexování polí po změně pořadí nebo smazání
        function reindexRows() {
            tableBody.find('tr.radio-row').each(function(index) {
                $(this).find('input').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        // Přidání nového řádku
        $('#add-radio-row').on('click', function() {
            var newIndex = tableBody.find('tr.radio-row').length;
            var newRow = '<tr class="radio-row">' +
                '<td class="sort-handle"><span class="dashicons dashicons-menu"></span></td>' +
                '<td><input type="text" name="pehobr_internet_radia[' + newIndex + '][nazev]" class="large-text" placeholder="Např. Rádio Proglas" required></td>' +
                '<td><input type="url" name="pehobr_internet_radia[' + newIndex + '][stream_url]" class="large-text" placeholder="https://icecast.proglas.cz/proglas128.mp3" required></td>' +
                '<td><input type="url" name="pehobr_internet_radia[' + newIndex + '][image_url]" class="large-text" placeholder="URL k logu rádia"></td>' +
                '<td><button type="button" class="button button-secondary remove-radio-row">Odebrat</button></td>' +
                '</tr>';
            tableBody.append(newRow);
        });

        // Odebrání řádku
        tableBody.on('click', '.remove-radio-row', function() {
            $(this).closest('tr').remove();
            reindexRows();
        });

        // Implementace "drag and drop" pro změnu pořadí
        tableBody.sortable({
            handle: '.sort-handle',
            axis: 'y',
            update: function(event, ui) {
                reindexRows();
            }
        });
    });
    </script>
    <?php
}