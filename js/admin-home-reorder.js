jQuery(document).ready(function($) {
    const sortableList = $('#sortable-home-sections');

    if (sortableList.length) {

        function saveLayoutSettings() {
            const feedbackDiv = $('#reorder-feedback');
            
            // Získání pořadí
            const sectionsOrder = sortableList.sortable('toArray');
            
            // Získání stavu viditelnosti
            const visibility = {};
            sortableList.find('li').each(function() {
                const slug = $(this).attr('id');
                const isChecked = $(this).find('.visibility-toggle').is(':checked');
                visibility[slug] = isChecked ? 'on' : 'off';
            });

            feedbackDiv.html('<p class="notice notice-info">Ukládám nastavení...</p>').show();

            $.ajax({
                url: pehobr_home_reorder_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'pehobr_save_home_layout_settings',
                    nonce: pehobr_home_reorder_ajax.nonce,
                    order: sectionsOrder,
                    visibility: visibility
                },
                success: function(response) {
                    const noticeClass = response.success ? 'notice-success' : 'notice-error';
                    const message = response.success ? response.data : 'Chyba: ' + (response.data || 'Neznámá chyba');
                    feedbackDiv.html('<p class="notice ' + noticeClass + ' is-dismissible">' + message + '</p>');

                    setTimeout(function() {
                        feedbackDiv.fadeOut('slow');
                    }, 3000);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    feedbackDiv.html('<p class="notice notice-error is-dismissible">Došlo k chybě při komunikaci se serverem: ' + textStatus + ' - ' + errorThrown + '</p>');
                }
            });
        }

        // Inicializace drag&drop
        sortableList.sortable({
            handle: ".handle",
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                saveLayoutSettings();
            }
        });

        // Uložení při změně přepínače
        sortableList.on('change', '.visibility-toggle', function() {
            saveLayoutSettings();
        });
    }
});