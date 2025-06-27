/**
 * JavaScript pro úvodní stránku (page-home)
 * Zajišťuje funkčnost modálního okna a ukládání oblíbených.
 * VERZE 4: Ukládá citát i se jménem autora.
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

    let currentQuoteId = null;
    let currentAuthorName = null; // Přidáno pro uložení jména autora

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

        // Sestavíme kompletní HTML, které se uloží - včetně autora
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
            // Uložíme nově sestavený obsah
            favorites.push({ id: currentQuoteId, content: finalContent });
            favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
            favoriteBtn.addClass('is-favorite');
        }
        saveFavorites(favorites);
    }

    // --- 3. Funkce pro otevření a zavření modálního okna ---
    function openModal(targetId, type, authorName) {
        currentQuoteId = targetId;
        currentAuthorName = authorName; // Uložíme si jméno autora
        const contentHtml = $('#' + targetId).html();

        if (type === 'video') {
            favoriteBtn.hide();
            modalContent.html(`<div class="responsive-video-wrapper">${contentHtml}</div>`);
            modalContainer.addClass('video-modal').removeClass('audio-modal');
        } else if (type === 'audio') {
            favoriteBtn.hide();
            modalContent.html(contentHtml);
            modalContainer.addClass('audio-modal').removeClass('video-modal');
        } else { // Pro textové citáty
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
        modalContainer.fadeIn(300);
        modalContainer.addClass('is-visible');
    }

    function closeModal() {
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
            currentAuthorName = null; // Vynulujeme i jméno autora
        });
    }

    // --- 4. Přidání posluchačů událostí ---
    gridItems.on('click', function(event) {
        const targetId = $(this).data('target-id');
        const type = $(this).data('type');
        const author = $(this).data('author-name'); // Načteme jméno autora
        if (targetId) {
            event.preventDefault();
            openModal(targetId, type, author); // Předáme jméno autora
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