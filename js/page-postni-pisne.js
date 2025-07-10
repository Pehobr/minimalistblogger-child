jQuery(document).ready(function($) {
    const overlay = $('#song-modal-overlay');
    const modalContainer = $('#song-modal-container');
    const modalTitle = $('#song-modal-title');
    const modalAudio = $('#song-modal-audio');
    const modalImage = $('#song-modal-image');
    const modalText = $('#song-modal-text');
    const closeBtn = $('#song-modal-close-btn');
    const modalContent = $('#song-modal-content');

    let touchStartY = 0;

    const handleTouchStart = (e) => {
        if (e.touches.length === 1) {
            touchStartY = e.touches[0].clientY;
        }
    };

    const handleTouchMove = (e) => {
        const isScrollingDown = e.touches[0].clientY > touchStartY;
        const isContentAtTop = modalContent.scrollTop() === 0;

        if (isContentAtTop && isScrollingDown) {
            e.preventDefault();
        }
    };

    function openModal(data) {
        modalTitle.text(data.title);
        modalAudio.attr('src', data.audio);
        modalImage.attr('src', data.image);
        modalText.text(data.text);
        
        overlay.fadeIn(200);
        modalContainer.fadeIn(300);

        modalContent[0].addEventListener('touchstart', handleTouchStart, { passive: false });
        modalContent[0].addEventListener('touchmove', handleTouchMove, { passive: false });
    }

    function closeModal() {
        overlay.fadeOut(200);
        modalContainer.fadeOut(300, function() {
            modalAudio[0].pause();
            modalAudio.attr('src', '');
        });

        modalContent[0].removeEventListener('touchstart', handleTouchStart, { passive: false });
        modalContent[0].removeEventListener('touchmove', handleTouchMove, { passive: false });
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