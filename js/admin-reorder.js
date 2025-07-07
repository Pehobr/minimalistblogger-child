jQuery(document).ready(function($) {
    if ($('#sortable-navody').length) {

        $('#sortable-navody').sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                var postOrder = $(this).sortable('toArray');
                var feedbackDiv = $('#reorder-feedback');

                feedbackDiv.html('<p class="notice notice-info">Ukládám pořadí...</p>').show();

                $.ajax({
                    url: pehobr_reorder_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'pehobr_save_page_order',
                        nonce: pehobr_reorder_ajax.nonce,
                        order: postOrder
                    },
                    success: function(response) {
                        var noticeClass = response.success ? 'notice-success' : 'notice-error';
                        var message = response.success ? response.data : 'Chyba: ' + response.data;
                        feedbackDiv.html('<p class="notice ' + noticeClass + ' is-dismissible">' + message + '</p>');

                        setTimeout(function() {
                            feedbackDiv.fadeOut('slow');
                        }, 3000);
                    },
                    error: function() {
                        feedbackDiv.html('<p class="notice notice-error is-dismissible">Došlo k chybě při komunikaci se serverem.</p>');
                    }
                });
            }
        });
    }
});