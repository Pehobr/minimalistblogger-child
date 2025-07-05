jQuery(document).ready(function($) {
    const overlay = $('#video-modal-overlay');
    const modalContainer = $('#video-modal-container');
    const modalContent = $('#video-modal-content');
    const closeBtn = $('#video-modal-close-btn');

    function openModal(embedUrl) {
        const iframeHtml = `<iframe src="${embedUrl}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
        modalContent.html(iframeHtml);
        overlay.fadeIn(200);
        modalContainer.fadeIn(300);
    }

    function closeModal() {
        overlay.fadeOut(200);
        modalContainer.fadeOut(300, function() {
            modalContent.empty(); // Důležité pro zastavení videa
        });
    }

    $('.video-grid-item').on('click', function() {
        const embedUrl = $(this).data('embed-url');
        if (embedUrl) {
            openModal(embedUrl);
        }
    });

    closeBtn.on('click', closeModal);
    overlay.on('click', closeModal);

    $(document).on('keydown', function(e) {
        if (e.key === "Escape" && modalContainer.is(':visible')) {
            closeModal();
        }
    });
});