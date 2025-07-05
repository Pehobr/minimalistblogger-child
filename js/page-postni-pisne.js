jQuery(document).ready(function($) {
    const overlay = $('#song-modal-overlay');
    const modalContainer = $('#song-modal-container');
    const modalTitle = $('#song-modal-title');
    const modalAudio = $('#song-modal-audio');
    const modalImage = $('#song-modal-image');
    const modalText = $('#song-modal-text');
    const closeBtn = $('#song-modal-close-btn');

    function openModal(data) {
        modalTitle.text(data.title);
        modalAudio.attr('src', data.audio);
        modalImage.attr('src', data.image);
        modalText.text(data.text);
        
        overlay.fadeIn(200);
        modalContainer.fadeIn(300);
    }

    function closeModal() {
        overlay.fadeOut(200);
        modalContainer.fadeOut(300, function() {
            modalAudio[0].pause(); // Zastaví přehrávání
            modalAudio.attr('src', ''); // Vymaže zdroj audia
        });
    }

    $('.song-item-button').on('click', function() {
        const data = {
            title: $(this).data('title'),
            audio: $(this).data('audio'),
            image: $(this).data('image'),
            text: $(this).data('text')
        };
        openModal(data);
    });

    closeBtn.on('click', closeModal);
    overlay.on('click', closeModal);

    $(document).on('keydown', function(e) {
        if (e.key === "Escape" && modalContainer.is(':visible')) {
            closeModal();
        }
    });
});