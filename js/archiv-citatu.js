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

    // --- ČÁST 2: NOVÁ FUNKČNOST PRO OBLÍBENÉ POLOŽKY ---
    const favoritesStorageKey = 'pehobr_favorite_quotes';

    // Funkce pro získání oblíbených z localStorage
    function getFavorites() {
        const favoritesJSON = localStorage.getItem(favoritesStorageKey);
        return favoritesJSON ? JSON.parse(favoritesJSON) : [];
    }

    // Funkce pro uložení oblíbených do localStorage
    function saveFavorites(favorites) {
        localStorage.setItem(favoritesStorageKey, JSON.stringify(favorites));
    }

    // Zkontroluje, zda je citát již v oblíbených
    function isFavorite(quoteId) {
        const favorites = getFavorites();
        return favorites.some(fav => fav.id === quoteId);
    }

    // Přepne stav oblíbenosti pro daný citát
    function toggleFavorite(quoteId) {
        let favorites = getFavorites();
        const quoteElement = $('#' + quoteId);
        // Získáme pouze HTML obsah bloku s textem a autorem
        const quoteHtmlContent = quoteElement.find('.citat-content-wrapper').html();
        const favoriteBtn = quoteElement.find('.archive-favorite-btn');
        const favoriteIcon = favoriteBtn.find('i');

        if (isFavorite(quoteId)) {
            // Odebrat z oblíbených
            favorites = favorites.filter(fav => fav.id !== quoteId);
            favoriteIcon.removeClass('fa-star').addClass('fa-star-o');
            favoriteBtn.removeClass('is-favorite');
        } else {
            // Přidat do oblíbených
            if (quoteHtmlContent) {
                favorites.push({ id: quoteId, content: quoteHtmlContent });
                favoriteIcon.removeClass('fa-star-o').addClass('fa-star');
                favoriteBtn.addClass('is-favorite');
            }
        }
        saveFavorites(favorites);
    }

    // Inicializace stavu hvězdiček při načtení stránky
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

    // Posluchač události pro kliknutí na tlačítko oblíbených
    // Používáme delegování událostí, aby to fungovalo i po filtrování Isotopem
    $('#citat-list').on('click', '.archive-favorite-btn', function() {
        const quoteId = $(this).data('id');
        toggleFavorite(quoteId);
    });

    // Spustíme inicializaci po načtení stránky
    initializeFavoriteButtons();
});