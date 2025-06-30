jQuery(function($) {
    // --- ČÁST 1: FUNKČNOST FILTROVÁNÍ (ISOTOPE) ---
    $(window).on('load', function() {
        var $grid = $('#citat-list').isotope({
            itemSelector: '.citat-item',
            layoutMode: 'vertical'
        });

        $('#citat-filters').on('click', 'button', function() {
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({ filter: filterValue });
            $('#citat-filters button').removeClass('active');
            $(this).addClass('active');
        });
    });

    // --- ČÁST 2: FUNKČNOST PRO OBLÍBENÉ POLOŽKY (UPRAVENO) ---
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

    function toggleFavorite(quoteId) {
        let favorites = getFavorites();
        const quoteElement = $('#' + quoteId);
        const favoriteBtn = quoteElement.find('.archive-favorite-btn');
        const favoriteIcon = favoriteBtn.find('i');

        if (isFavorite(quoteId)) {
            // Odebrat z oblíbených
            favorites = favorites.filter(fav => fav.id !== quoteId);
            favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
            favoriteBtn.removeClass('is-favorite');
        } else {
            // Přidat do oblíbených
            const quoteTextHtml = quoteElement.find('.citat-text').html();
            const authorName = quoteElement.find('.citat-author').text();

            // Sestavíme obsah pro uložení, aby odpovídal formátu z úvodní stránky
            const finalContent = `
                <blockquote class="citat-text">
                    ${quoteTextHtml}
                </blockquote>
                <footer class="citat-meta">
                    <span class="citat-author">${authorName}</span>
                </footer>
            `;

            if (quoteTextHtml) {
                favorites.push({ id: quoteId, content: finalContent });
                favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
                favoriteBtn.addClass('is-favorite');
            }
        }
        saveFavorites(favorites);
    }

    function initializeFavoriteButtons() {
        $('.archive-favorite-btn').each(function() {
            const btn = $(this);
            const quoteId = btn.data('id');
            if (isFavorite(quoteId)) {
                btn.addClass('is-favorite');
                btn.find('i').removeClass('fa-star-o').addClass('fa-star');
            }
        });
    }

    $('#citat-list').on('click', '.archive-favorite-btn', function() {
        const quoteId = $(this).data('id');
        toggleFavorite(quoteId);
    });

    initializeFavoriteButtons();
});