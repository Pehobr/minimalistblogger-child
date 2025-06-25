/**
 * JavaScript pro stránku s oblíbenými texty (page-oblibene)
 * Verze s ikonou pro smazání.
 */
jQuery(document).ready(function($) {

    const container = $('#favorites-container');
    const favoritesStorageKey = 'pehobr_favorite_quotes';

    function getFavorites() {
        const favoritesJSON = localStorage.getItem(favoritesStorageKey);
        return favoritesJSON ? JSON.parse(favoritesJSON) : [];
    }

    function saveFavorites(favorites) {
        localStorage.setItem(favoritesStorageKey, JSON.stringify(favorites));
    }

    function renderFavorites() {
        const favorites = getFavorites();
        container.empty(); // Vyčistíme kontejner před novým vykreslením

        if (favorites.length === 0) {
            container.html('<div class="favorites-empty-message"><p>Zatím zde nemáte žádné oblíbené texty.</p><p>Text si můžete přidat kliknutím na hvězdičku u citátu na úvodní stránce.</p></div>');
            return;
        }

        favorites.forEach(fav => {
            // ZMĚNA ZDE: Místo textového tlačítka vkládáme ikonu koše
            const favoriteItemHTML = `
                <div class="favorite-item" id="favorite-${fav.id}">
                    <div class="favorite-item-content">
                        ${fav.content}
                    </div>
                    <button class="delete-favorite-btn" data-id="${fav.id}" aria-label="Smazat oblíbenou položku">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            `;
            container.append(favoriteItemHTML);
        });
    }

    function deleteFavorite(quoteId) {
        let favorites = getFavorites();
        favorites = favorites.filter(fav => fav.id !== quoteId);
        saveFavorites(favorites);

        // Plynulé odstranění prvku z DOM
        $(`#favorite-${quoteId}`).fadeOut(400, function() {
            $(this).remove();
            // Pokud po smazání nezůstaly žádné položky, zobrazíme zprávu
            if (getFavorites().length === 0) {
                renderFavorites();
            }
        });
    }

    // Delegovaný event listener pro tlačítka "Vymazat"
    container.on('click', '.delete-favorite-btn', function() {
        const quoteIdToDelete = $(this).data('id');
        // Můžeme se zeptat na potvrzení, aby uživatel nesmazal položku omylem
        if (confirm('Opravdu si přejete odstranit tento text z oblíbených?')) {
            deleteFavorite(quoteIdToDelete);
        }
    });
    
    // První vykreslení při načtení stránky
    renderFavorites();

});