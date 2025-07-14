jQuery(document).ready(function($) {
    "use strict";

    // --- BLOK PRO UŽIVATELSKÉ NASTAVENÍ ---
    if ($('body').hasClass('page-template-page-home')) {
        const themeStorageKey = 'pehobr_user_home_themes';
        const savedThemes = localStorage.getItem(themeStorageKey);
        const themeSettings = savedThemes ? JSON.parse(savedThemes) : {};

        // Mapa slugů sekcí na jejich selektory v HTML
        const sectionSelectors = {
            'pope_section': '.pope-section-container',
            'saints_section': '.saints-section-container',
            'actions_section': '.third-row-section-container',
            'desktop_nav_section': '#desktop-nav-grid-container',
            'library_section': '#library-grid-container',
        };

        // Aplikace barevnosti pro každou sekci individuálně
        for (const [slug, selector] of Object.entries(sectionSelectors)) {
            const theme = themeSettings[slug] || 'fialove'; // Výchozí je fialová
            const container = $(selector);
            if(container.length) {
                container.removeClass('style-svetle style-fialove').addClass('style-' + theme);

                // Speciální logika pro výměnu ikon v sekci "Akce"
                if (slug === 'actions_section') {
                    container.find('.icon-grid-item img').each(function() {
                        const icon = $(this);
                        let currentSrc = icon.attr('src');
                        if (!currentSrc) return;
                        const isLightIcon = currentSrc.includes('-svetla.png');

                        if (theme === 'fialove') {
                            if (!isLightIcon && currentSrc.includes('.png')) {
                                icon.attr('src', currentSrc.replace('.png', '-svetla.png'));
                            }
                        } else { // theme je 'svetle'
                            if (isLightIcon) {
                                icon.attr('src', currentSrc.replace('-svetla.png', '.png'));
                            }
                        }
                    });
                }

                // Speciální logika pro výměnu ikon v sekci "Knihovny"
                if (slug === 'library_section') {
                    container.find('img').each(function() {
                        const img = $(this);
                        const darkIcon = img.data('dark-icon');
                        const lightIcon = img.data('light-icon');
                        if (theme === 'svetle' && lightIcon) {
                            img.attr('src', lightIcon);
                        } else if (darkIcon) {
                            img.attr('src', darkIcon);
                        }
                    });
                }
            }
        }

        // Nastavení viditelnosti sekcí (zůstává beze změny)
        const visibilityStorageKey = 'pehobr_user_home_visibility';
        const savedVisibility = localStorage.getItem(visibilityStorageKey);
        if (savedVisibility) {
            const visibilitySettings = JSON.parse(savedVisibility);
            for (const [slug, isVisible] of Object.entries(visibilitySettings)) {
                if (isVisible === 'off' && sectionSelectors[slug]) {
                    $(sectionSelectors[slug]).hide();
                }
            }
        }

        // Nastavení zobrazení pro sekci papežů (zůstává beze změny)
        const displayStorageKey = 'pehobr_user_home_display';
        const savedDisplay = localStorage.getItem(displayStorageKey);
        const displaySettings = savedDisplay ? JSON.parse(savedDisplay) : {};
        const popeContainer = $('.pope-section-container');
        const userPopeView = displaySettings['pope_section_display'];
        const defaultPopeView = popeContainer.data('default-view');
        const finalPopeView = userPopeView || defaultPopeView;
        if (finalPopeView === 'textove') {
            popeContainer.find('.view-graficke').hide();
            popeContainer.find('.view-textove').show();
            popeContainer.addClass('view-textove-active');
        } else {
            popeContainer.find('.view-textove').hide();
            popeContainer.find('.view-graficke').show();
            popeContainer.removeClass('view-textove-active');
        }
    }
    // --- KONEC BLOKU PRO UŽIVATELSKÉ NASTAVENÍ ---

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
    let currentRawContent = null;

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
    
    function openAiInspirationModal() {
        currentAuthorName = 'Inspirace';
        bodyElement.addClass('modal-is-open');
        modalContainer.removeClass('video-modal audio-modal image-modal');

        modalContent.html('<div class="modal-loader"></div><p style="text-align:center; font-size: 0.9em; color: #666;">Načítám denní čtení a generuji inspiraci...</p>');
        favoriteBtn.hide();

        modalOverlay.fadeIn(200);
        modalContainer.css('display', 'flex').hide().fadeIn(300);
        modalContainer.addClass('is-visible');
        
        $('<div>').load('/poboznosti/ .entry-content', function(response, status, xhr) {
            let scriptureText = 'Pro dnešní den není k dispozici žádné čtení.';
            if (status !== "error") {
                const tempDiv = $('<div>').html(response);
                tempDiv.find('h1, h2, h3, .audio-player-button, .posts-entry > .entry-header').remove();
                scriptureText = tempDiv.text().trim();
            }

            const lifeSituationsKey = 'pehobr_life_situations';
            const savedSituationsData = localStorage.getItem(lifeSituationsKey);
            const savedSituations = savedSituationsData ? JSON.parse(savedSituationsData) : {};
            const activeSituations = Object.keys(savedSituations).filter(key => savedSituations[key]);
            const situationsString = activeSituations.join(', ').replace(/_/g, ' ') || 'Uživatel hledá povzbuzení.';

            $.ajax({
                url: pehobr_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'pehobr_generate_home_inspiration',
                    nonce: pehobr_ajax.nonce,
                    scripture: scriptureText,
                    situations: situationsString
                },
                success: function(response) {
                    if (response.success && response.data.content) {
                        const generatedContent = response.data.content;
                        currentRawContent = generatedContent;
                        currentQuoteId = 'ai_inspiration_' + new Date().getTime();

                        modalContent.html(generatedContent).css('text-align', 'left');

                        favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
                        favoriteBtn.removeClass('is-favorite');
                        favoriteBtn.show();
                    } else {
                        const errorMessage = response.data.content || 'Omlouváme se, inspiraci se nepodařilo vygenerovat.';
                        modalContent.html('<p>' + errorMessage + '</p>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Chyba:", textStatus, errorThrown);
                    modalContent.html('<p>Došlo k chybě při komunikaci se serverem.</p>');
                }
            });
        });
    }

    function openModal(targetId, type, authorName) {
        currentQuoteId = targetId;
        currentAuthorName = authorName;
        const contentSource = $('#' + targetId);
        const contentHtml = contentSource.html();
        currentRawContent = contentHtml;

        bodyElement.addClass('modal-is-open');
        modalContainer.removeClass('video-modal audio-modal image-modal');

        if (type === 'image') {
            favoriteBtn.hide();
            modalContent.html(contentHtml);
            modalContainer.addClass('image-modal');
        } else if (type === 'video') {
            favoriteBtn.hide();
            modalContent.html(`<div class="responsive-video-wrapper">${contentHtml}</div>`);
            modalContainer.addClass('video-modal');
        } else {
            favoriteBtn.show();
            modalContent.html(contentHtml);
            if (isFavorite(currentQuoteId)) {
                favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
                favoriteBtn.addClass('is-favorite');
            } else {
                favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
                favoriteBtn.removeClass('is-favorite');
            }
            if (targetId === 'quote-content-modlitba_text') {
                 favoriteBtn.hide();
            }
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
            currentQuoteId = null;
            currentAuthorName = null;
            currentRawContent = null;
        });
        modalOverlay.fadeOut(250);
    }

    $('.icon-grid-item, .pope-icon-link').on('click', function(e) {
        const link = $(this);
        const type = link.data('type');
        
        if (link.attr('href') !== '#') return;
        
        e.preventDefault();

        if (type === 'ai_inspiration') {
            openAiInspirationModal();
        } else {
            const targetId = link.data('target-id');
            const authorName = link.data('author-name');
            if (targetId) openModal(targetId, type, authorName);
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
        if (!currentQuoteId || !currentAuthorName || !currentRawContent) return;
        if (isFavorite(currentQuoteId)) {
            removeFavorite(currentQuoteId);
            favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
            $(this).removeClass('is-favorite');
        } else {
            const finalContent = `<div class="citat-text">${currentRawContent}</div><footer class="citat-meta"><span class="citat-author">${currentAuthorName}</span></footer>`;
            addFavorite(currentQuoteId, currentAuthorName, finalContent);
            favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
            $(this).addClass('is-favorite');
        }
    });
    
    if (typeof donation_popup_settings !== 'undefined' && donation_popup_settings.show_popup) {
        if (!sessionStorage.getItem('pehobr_donation_popup_shown')) {
            const popup = $('#donation-popup-container');
            const overlay = $('#donation-popup-overlay');
            const timerSpan = $('#donation-timer');
            let countdown = 7;
            const showPopup = () => {
                timerSpan.text(countdown + 's');
                popup.css('display', 'block');
                overlay.css('display', 'block');
                const interval = setInterval(() => {
                    countdown--;
                    timerSpan.text(countdown + 's');
                    if (countdown <= 0) {
                        clearInterval(interval);
                        timerSpan.text(' Zavřít ');
                        overlay.on('click', hidePopup);
                    }
                }, 1000);
            };
            const hidePopup = () => {
                popup.fadeOut();
                overlay.fadeOut();
                sessionStorage.setItem('pehobr_donation_popup_shown', 'true');
            };
            setTimeout(showPopup, 2000);
        }
    }
});