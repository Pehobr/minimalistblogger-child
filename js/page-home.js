jQuery(document).ready(function($) {
    "use strict";

     // --- BLOK PRO UŽIVATELSKÉ NASTAVENÍ VIDITELNOSTI ---
    // Tento kód se aplikuje pouze na úvodní stránce
    if ($('body').hasClass('page-template-page-home')) {
        const visibilityStorageKey = 'pehobr_user_home_visibility';
        const savedVisibility = localStorage.getItem(visibilityStorageKey);

        if (savedVisibility) {
            const visibilitySettings = JSON.parse(savedVisibility);
            
            // Mapování slugů na ID kontejnerů na stránce
            const sectionMap = {
                'pope_section': '.pope-section-container',
                'saints_section': '.saints-section-container',
                'actions_section': '.third-row-section-container',
                'desktop_nav_section': '#desktop-nav-grid-container',
                'library_section': '#library-grid-container'
            };

            for (const [slug, isVisible] of Object.entries(visibilitySettings)) {
                if (isVisible === 'off' && sectionMap[slug]) {
                    $(sectionMap[slug]).hide();
                }
            }
        }
    }
    // --- KONEC BLOKU PRO NASTAVENÍ VIDITELNOSTI ---

    const bodyElement = $('body');
    const modalContainer = $('#quote-modal-container');
    const modalOverlay = $('#quote-modal-overlay');
    const modalContent = $('#quote-modal-content');
    const favoriteBtn = $('#quote-modal-favorite-btn');
    const favoriteIcon = favoriteBtn.find('i');
    
    const favoritesStorageKey = 'pehobr_favorite_quotes';
    let favorites = JSON.parse(localStorage.getItem(favoritesStorageKey)) || [];
    let currentQuoteId = null;
    let currentAuthorName = null;

    function isFavorite(quoteId) {
        return favorites.some(fav => fav.id === quoteId);
    }

    function saveFavorites() {
        localStorage.setItem(favoritesStorageKey, JSON.stringify(favorites));
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

        // Reset all modal type classes first
        modalContainer.removeClass('video-modal audio-modal image-modal');

        if (type === 'image') {
            favoriteBtn.hide();
            modalContent.html(contentHtml);
            modalContainer.addClass('image-modal');
        } else if (type === 'video') {
            favoriteBtn.hide();
            modalContent.html(`<div class="responsive-video-wrapper">${contentHtml}</div>`);
            modalContainer.addClass('video-modal');
        } else { // This handles 'text' and 'audio' which is part of the text content
            favoriteBtn.show();
            modalContent.html(contentHtml);
            if (isFavorite(currentQuoteId)) {
                favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
                favoriteBtn.addClass('is-favorite');
            } else {
                favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
                favoriteBtn.removeClass('is-favorite');
            }
             // Hide favorite button for prayer
            if (targetId === 'quote-content-modlitba_text') {
                favoriteBtn.hide();
            }
        }
        
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
            modalContainer.removeClass('is-visible video-modal audio-modal image-modal');
        });
        modalOverlay.fadeOut(250);
    }

    $('.icon-grid-item, .pope-icon-link').on('click', function(e) {
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
        if (e.key === "Escape" && modalContainer.hasClass('is-visible')) {
            closeModal();
        }
    });

    favoriteBtn.on('click', function() {
        if (currentQuoteId) {
            if (isFavorite(currentQuoteId)) {
                removeFavorite(currentQuoteId);
                favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
                $(this).removeClass('is-favorite');
            } else {
                const rawQuoteHtml = modalContent.html();
                const authorName = currentAuthorName;
                const finalContent = `<blockquote class="citat-text">${rawQuoteHtml}</blockquote><footer class="citat-meta"><span class="citat-author">${authorName}</span></footer>`;
                addFavorite(currentQuoteId, authorName, finalContent);
                favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
                $(this).addClass('is-favorite');
            }
        }
    });

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    function initializeCustomPlayer(playerElement) {
        const audioSrc = playerElement.data('audio-src');
        if (!audioSrc) return;
        const audio = new Audio(audioSrc);
        const playBtn = playerElement.find('.map-play-pause-btn');
        const playIcon = playBtn.find('i');
        const slider = playerElement.find('.map-seek-slider');
        const currentTimeEl = playerElement.find('.map-current-time');
        const durationEl = playerElement.find('.map-duration');
        playBtn.on('click', () => audio.paused ? audio.play() : audio.pause());
        audio.addEventListener('play', () => playIcon.removeClass('fa-play').addClass('fa-pause'));
        audio.addEventListener('pause', () => playIcon.removeClass('fa-pause').addClass('fa-play'));
        audio.addEventListener('ended', () => { playIcon.removeClass('fa-pause').addClass('fa-play'); audio.currentTime = 0; slider.val(0); });
        audio.addEventListener('loadedmetadata', () => { durationEl.text(formatTime(audio.duration)); slider.attr('max', audio.duration); });
        audio.addEventListener('timeupdate', () => { currentTimeEl.text(formatTime(audio.currentTime)); slider.val(audio.currentTime); });
        slider.on('input', () => audio.currentTime = slider.val());
        $('#quote-modal-close-btn, #quote-modal-overlay').one('click.audioPlayer', () => { if (audio) { audio.pause(); audio.src = ''; } });
    }

    // === OPRAVENÁ A UPRAVENÁ LOGIKA PRO VYSKAKOVACÍ OKNO ===

    // Zkontrolujeme, zda existuje objekt s nastavením a zda je povoleno zobrazení
    if (typeof donation_popup_settings !== 'undefined' && donation_popup_settings.show_popup) {
        
        // Zkontrolujeme, zda okno již nebylo v této session zobrazeno
        if (!sessionStorage.getItem('pehobr_donation_popup_shown')) {
            
            const donationOverlay = $('#donation-popup-overlay');
            const donationContainer = $('#donation-popup-container');
            const timerElement = $('#donation-timer'); 

            let countdown = 7; // Nová startovní hodnota odpočtu
            let countdownInterval;

            // Funkce pro zavření okna
            function closeDonationPopup() {
                clearInterval(countdownInterval); // Důležité: zastavíme odpočet, aby neběžel na pozadí
                donationOverlay.fadeOut(300);
                donationContainer.fadeOut(300);
            }

            // Funkce pro spuštění a aktualizaci odpočtu
            function startCountdown() {
                timerElement.text(countdown + ' s'); // Nastavíme počáteční text

                countdownInterval = setInterval(function() {
                    countdown--; // Snížíme hodnotu o 1
                    if (countdown > 0) {
                        timerElement.text(countdown + ' s'); // Aktualizujeme text, dokud je odpočet > 0
                    } else {
                        // Když odpočet dojde na nulu, zavřeme okno a vyčistíme interval
                        closeDonationPopup();
                    }
                }, 1000); // Opakovat každou vteřinu
            }

            // Zobrazíme okno a překrytí
            donationOverlay.fadeIn(300);
            donationContainer.fadeIn(300, function() {
                // Spustíme odpočet až po dokončení animace zobrazení
                startCountdown();
            });

            // Označíme, že v této session již bylo okno zobrazeno
            sessionStorage.setItem('pehobr_donation_popup_shown', 'true');

            // Zavření po kliknutí na překryvnou vrstvu (mimo okno)
            donationOverlay.on('click', function() {
                closeDonationPopup(); 
            });
        }
    }
});