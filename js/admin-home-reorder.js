jQuery(document).ready(function($) {
    // Zkontrolujeme, zda na stránce existuje kontejner pro řazení
    if ($('#sortable-home-sections').length) {

        $('#sortable-home-sections').sortable({
            placeholder: "ui-state-highlight", // Třída pro zvýraznění místa, kam se položka přesouvá
            update: function(event, ui) {
                // Získáme nové pořadí IDček položek
                var sectionsOrder = $(this).sortable('toArray');
                var feedbackDiv = $('#reorder-feedback');

                // Zobrazíme zprávu o ukládání
                feedbackDiv.html('<p class="notice notice-info">Ukládám pořadí...</p>').show();

                // Odešleme data pomocí AJAXu
                $.ajax({
                    url: pehobr_home_reorder_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'pehobr_save_home_layout_order',
                        nonce: pehobr_home_reorder_ajax.nonce,
                        order: sectionsOrder // Pole s novým pořadím
                    },
                    success: function(response) {
                        // Zobrazíme zprávu o úspěchu nebo chybě
                        var noticeClass = response.success ? 'notice-success' : 'notice-error';
                        var message = response.success ? response.data : 'Chyba: ' + response.data;
                        feedbackDiv.html('<p class="notice ' + noticeClass + ' is-dismissible">' + message + '</p>');

                        // Po 3 sekundách zprávu skryjeme
                        setTimeout(function() {
                            feedbackDiv.fadeOut('slow');
                        }, 3000);
                    },
                    error: function() {
                        // Zpráva pro případ chyby komunikace
                        feedbackDiv.html('<p class="notice notice-error is-dismissible">Došlo k chybě při komunikaci se serverem.</p>');
                    }
                });
            }
        });
    }
});