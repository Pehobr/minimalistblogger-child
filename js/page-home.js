/**
 * JavaScript pro úvodní stránku (page-home)
 * Zajišťuje funkčnost modálního okna a ukládání oblíbených.
 * VERZE 5: Přidána třída na body pro opravu dotykového posouvání.
 */
jQuery(document).ready(function($) {

    // --- 1. Selekce všech potřebných prvků ---
    const gridItems = $('.icon-grid-item');
    const modalOverlay = $('#quote-modal-overlay');
    const modalContainer = $('#quote-modal-container');
    const modalContent = $('#quote-modal-content');
    const closeModalBtn = $('#quote-modal-close-btn');
    const favoriteBtn = $('#quote-modal-favorite-btn');
    const favoriteIcon = favoriteBtn.find('i');
    const bodyElement = $('body'); // Uložíme si tělo stránky

    let currentQuoteId = null;
    let currentAuthorName = null;

    // --- 2. Správa oblíbených v LocalStorage ---
    const favoritesStorageKey = 'pehobr_favorite_quotes';

    function getFavorites() {
        const favoritesJSON = localStorage.getItem(favoritesStorageKey);
        return favoritesJSON ? JSON.parse(favoritesJSON) : [];
    }

    function saveFavorites(favorites) {
        localStorage.setItem(favoritesStorageKey, JSON.stringify(favorites));
    }

    function isFavorite(quoteId) {
        const favorites = getFavorites();
        return favorites.some(fav => fav.id === quoteId);
    }

    function toggleFavorite() {
        if (!currentQuoteId) return;
        let favorites = getFavorites();
        const quoteText = $('#' + currentQuoteId).html();
        const finalContent = `
            <blockquote class="citat-text">
                ${quoteText}
            </blockquote>
            <footer class="citat-meta">
                <span class="citat-author">${currentAuthorName}</span>
            </footer>
        `;

        if (isFavorite(currentQuoteId)) {
            favorites = favorites.filter(fav => fav.id !== currentQuoteId);
            favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
            favoriteBtn.removeClass('is-favorite');
        } else {
            favorites.push({ id: currentQuoteId, content: finalContent });
            favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
            favoriteBtn.addClass('is-favorite');
        }
        saveFavorites(favorites);
    }

    // --- 3. Funkce pro otevření a zavření modálního okna (UPRAVENO) ---
    function openModal(targetId, type, authorName) {
        currentQuoteId = targetId;
        currentAuthorName = authorName;
        const contentHtml = $('#' + targetId).html();

        // OPRAVA POSOUVÁNÍ: Přidáme třídu na body
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
                favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
                favoriteBtn.removeClass('is-favorite');
            }
        }
        
        modalOverlay.fadeIn(200);
        modalContainer.css('display', 'flex').hide().fadeIn(300); // Změna na display flex
        modalContainer.addClass('is-visible');
    }

    function closeModal() {
        // OPRAVA POSOUVÁNÍ: Odebereme třídu z body
        bodyElement.removeClass('modal-is-open');

        modalOverlay.fadeOut(300);
        modalContainer.fadeOut(200, function() {
            const mediaElement = modalContent.find('iframe, audio');
            if (mediaElement.length) {
                if (mediaElement.is('iframe')) {
                    mediaElement.attr('src', mediaElement.attr('src'));
                } else if (mediaElement.is('audio')) {
                    mediaElement[0].pause();
                }
            }
            modalContent.empty();
            modalContainer.removeClass('is-visible video-modal audio-modal');
            currentQuoteId = null;
            currentAuthorName = null;
        });
    }

    // --- 4. Přidání posluchačů událostí ---
    gridItems.on('click', function(event) {
        const targetId = $(this).data('target-id');
        const type = $(this).data('type');
        const author = $(this).data('author-name');
        if (targetId) {
            event.preventDefault();
            openModal(targetId, type, author);
        }
    });

    closeModalBtn.on('click', closeModal);
    modalOverlay.on('click', closeModal);
    favoriteBtn.on('click', toggleFavorite);

    $(document).on('keydown', function(event) {
        if (event.key === "Escape" && modalContainer.hasClass('is-visible')) {
            closeModal();
        }
    });
});