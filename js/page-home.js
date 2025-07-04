jQuery(document).ready(function($) {
    "use strict";

    const bodyElement = $('body');
    const modalContainer = $('#quote-modal-container');
    const modalOverlay = $('#quote-modal-overlay');
    const modalContent = $('#quote-modal-content');
    const favoriteBtn = $('#quote-modal-favorite-btn');
    const favoriteIcon = favoriteBtn.find('i');
    
    let favorites = JSON.parse(localStorage.getItem('favoriteQuotes')) || [];
    let currentQuoteId = null;
    let currentAuthorName = null;

    function isFavorite(quoteId) {
        return favorites.some(fav => fav.id === quoteId);
    }

    function saveFavorites() {
        localStorage.setItem('favoriteQuotes', JSON.stringify(favorites));
    }

    function addFavorite(quoteId, author, content) {
        if (!isFavorite(quoteId)) {
            favorites.push({ id: quoteId, author: author, content: content });
            saveFavorites();
        }
    }

    function removeFavorite(quoteId) {
        favorites = favorites.filter(fav => fav.id !== quoteId);
        saveFavorites();
    }

    function openModal(targetId, type, authorName) {
        currentQuoteId = targetId;
        currentAuthorName = authorName;
        const contentHtml = $('#' + targetId).html();

        bodyElement.addClass('modal-is-open');

        if (type === 'video') {
            favoriteBtn.hide();
            modalContent.html(`<div class="responsive-video-wrapper">${contentHtml}</div>`);
            modalContainer.addClass('video-modal').removeClass('audio-modal');
        } else if (type === 'audio') {
            favoriteBtn.hide();
            modalContent.html(contentHtml);
            modalContainer.addClass('audio-modal').removeClass('video-modal');
        } else {
            favoriteBtn.show();
            modalContent.html(contentHtml);
            modalContainer.removeClass('video-modal audio-modal');
            if (isFavorite(currentQuoteId)) {
                favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
                favoriteBtn.addClass('is-favorite');
            } else {
                favoriteIcon.removeClass('fa-star-o').addClass('fa-star-o');
                favoriteBtn.removeClass('is-favorite');
            }
        }
        
        // Inicializace vlastního audio přehrávače, pokud existuje v obsahu
        const customPlayer = modalContent.find('.modal-audio-player');
        if (customPlayer.length > 0) {
            initializeCustomPlayer(customPlayer);
        }

        modalOverlay.fadeIn(200);
        modalContainer.css('display', 'flex').hide().fadeIn(300);
        modalContainer.addClass('is-visible');
    }

    function closeModal() {
        bodyElement.removeClass('modal-is-open');
        modalContainer.fadeOut(200, function() {
            modalContent.empty();
            modalContainer.removeClass('is-visible video-modal audio-modal');
        });
        modalOverlay.fadeOut(250);
    }

    $('.icon-grid-item').on('click', function(e) {
        const targetId = $(this).data('target-id');
        if (targetId) {
            e.preventDefault();
            const type = $(this).data('type');
            const authorName = $(this).data('author-name');
            openModal(targetId, type, authorName);
        }
    });

    $('#quote-modal-close-btn, #quote-modal-overlay').on('click', function(e) {
        e.preventDefault();
        closeModal();
    });

    $(document).on('keydown', function(e) {
        if (e.key === "Escape") {
            if (modalContainer.hasClass('is-visible')) {
                closeModal();
            }
        }
    });

    favoriteBtn.on('click', function() {
        if (currentQuoteId) {
            if (isFavorite(currentQuoteId)) {
                removeFavorite(currentQuoteId);
                favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
                $(this).removeClass('is-favorite');
            } else {
                const quoteContent = modalContent.html();
                addFavorite(currentQuoteId, currentAuthorName, quoteContent);
                favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
                $(this).addClass('is-favorite');
            }
        }
    });

    // --- FUNKCE PRO VLASTNÍ AUDIO PŘEHRÁVAČ ---

    /**
     * Formátuje sekundy na formát m:ss (např. 1:05).
     * @param {number} seconds - Celkový počet sekund.
     * @returns {string} Čas ve formátu m:ss.
     */
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    /**
     * Inicializuje a ovládá vlastní audio přehrávač v modálním okně.
     * @param {jQuery} playerElement - jQuery objekt obalující přehrávač (.modal-audio-player).
     */
    function initializeCustomPlayer(playerElement) {
        const audioSrc = playerElement.data('audio-src');
        if (!audioSrc) return;

        const audio = new Audio(audioSrc);
        const playBtn = playerElement.find('.map-play-pause-btn');
        const playIcon = playBtn.find('i');
        const slider = playerElement.find('.map-seek-slider');
        const currentTimeEl = playerElement.find('.map-current-time');
        const durationEl = playerElement.find('.map-duration');

        playBtn.on('click', function() {
            if (audio.paused) {
                audio.play();
            } else {
                audio.pause();
            }
        });

        audio.addEventListener('play', () => {
            playIcon.removeClass('fa-play').addClass('fa-pause');
        });

        audio.addEventListener('pause', () => {
            playIcon.removeClass('fa-pause').addClass('fa-play');
        });
        
        audio.addEventListener('ended', () => {
            playIcon.removeClass('fa-pause').addClass('fa-play');
            audio.currentTime = 0;
            slider.val(0);
        });

        audio.addEventListener('loadedmetadata', () => {
            durationEl.text(formatTime(audio.duration));
            slider.attr('max', audio.duration);
        });

        audio.addEventListener('timeupdate', () => {
            currentTimeEl.text(formatTime(audio.currentTime));
            slider.val(audio.currentTime);
        });
        
        slider.on('input', function() {
            audio.currentTime = $(this).val();
        });

        // Ujistíme se, že se audio zastaví a uvolní zdroje při zavření modálního okna.
        // Používáme .one() k navázání události, která se spustí jen jednou,
        // abychom předešli vícenásobnému navázání při opakovaném otevírání modálu.
        $('#quote-modal-close-btn, #quote-modal-overlay').one('click.audioPlayer', function() {
            if (audio) {
                audio.pause();
                audio.src = ''; // Uvolníme zdroj, aby se audio nestahovalo na pozadí
            }
        });
    }

});