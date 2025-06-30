/**
 * JavaScript pro stránku s oblíbenými texty (page-oblibene)
 * Verze 4: Obrácené řazení (nejnovější nahoře).
 */
jQuery(document).ready(function($) {

    // --- SELEKCE PRVKŮ ---
    const container = $('#favorites-container');
    const exportBtn = $('#export-favorites-btn');
    const copyBtn = $('#copy-favorites-btn');
    const favoritesStorageKey = 'pehobr_favorite_quotes';

    // --- FUNKCE PRO PRÁCI S LOCALSTORAGE ---
    function getFavorites() {
        const favoritesJSON = localStorage.getItem(favoritesStorageKey);
        return favoritesJSON ? JSON.parse(favoritesJSON) : [];
    }

    function saveFavorites(favorites) {
        localStorage.setItem(favoritesStorageKey, JSON.stringify(favorites));
    }

    // --- FUNKCE PRO VYKRESLENÍ STRÁNKY (S OBRÁCENÝM POŘADÍM) ---
    function renderFavorites() {
        const favorites = getFavorites();
        container.empty();

        // === ZMĚNA ZDE: OTOČENÍ POŘADÍ ===
        // Nejnověji přidaná položka bude nyní na začátku pole.
        favorites.reverse();

        if (favorites.length === 0) {
            $('#favorites-actions').hide();
            container.html('<div class="favorites-empty-message"><p>Zatím zde nemáte žádné oblíbené texty.</p><p>Text si můžete přidat kliknutím na hvězdičku u citátu na úvodní stránce nebo v archivu.</p></div>');
            return;
        }

        $('#favorites-actions').show();
        favorites.forEach(fav => {
            const tempDiv = $('<div>').html(fav.content);
            const quoteTextHtml = tempDiv.find('.citat-text').html() || '';
            const authorName = tempDiv.find('.citat-author').text() || 'Neznámý autor';

            const favoriteItemHTML = `
                <div class="favorite-item" id="favorite-${fav.id}">
                    <div class="citat-text">
                        ${quoteTextHtml}
                    </div>
                    <footer class="favorite-item-footer">
                        <span class="citat-author">${authorName}</span>
                        <button class="delete-favorite-btn" data-id="${fav.id}" aria-label="Smazat oblíbenou položku">
                            <i class="fa fa-trash"></i>
                        </button>
                    </footer>
                </div>
            `;
            container.append(favoriteItemHTML);
        });
    }

    // --- FUNKCE PRO AKCE TLAČÍTEK ---
    function deleteFavorite(quoteId) {
        if (!confirm('Opravdu si přejete odstranit tento text z oblíbených?')) {
            return;
        }
        let favorites = getFavorites();
        favorites = favorites.filter(fav => fav.id !== quoteId);
        saveFavorites(favorites);

        $(`#favorite-${quoteId}`).fadeOut(400, function() {
            $(this).remove();
            if (getFavorites().length === 0) {
                renderFavorites();
            }
        });
    }

    function getFormattedFavoritesText() {
        const favorites = getFavorites();
        if (favorites.length === 0) return null;
        
        favorites.reverse(); // Zajistíme stejné pořadí i pro export

        return favorites.map(fav => {
            const tempDiv = $('<div>').html(fav.content);
            const text = (tempDiv.find('.citat-text').text() || "").trim();
            const author = (tempDiv.find('.citat-author').text() || "").trim();
            return `${text}\n- ${author}`;
        }).join('\n\n---\n\n');
    }

    function exportFavorites() {
        const text = getFormattedFavoritesText();
        if (!text) { alert('Není co exportovat.'); return; }
        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'oblibene_texty.txt';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function copyFavorites() {
        const text = getFormattedFavoritesText();
        if (!text) { alert('Není co kopírovat.'); return; }
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Všechny oblíbené texty byly zkopírovány do schránky.');
            }, () => {
                alert('Nepodařilo se zkopírovat texty.');
            });
        } else {
            alert('Váš prohlížeč nepodporuje automatické kopírování do schránky.');
        }
    }

    // --- PŘIŘAZENÍ EVENT LISTENERS ---
    container.on('click', '.delete-favorite-btn', function() {
        deleteFavorite($(this).data('id'));
    });
    exportBtn.on('click', exportFavorites);
    copyBtn.on('click', copyFavorites);
    
    // --- PRVNÍ VYKRESLENÍ ---
    renderFavorites();
});