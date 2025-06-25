/**
 * JavaScript pro stránku s oblíbenými texty (page-oblibene)
 * Verze s exportem do TXT a kopírováním do schránky.
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


    // --- FUNKCE PRO ZÍSKÁNÍ A FORMÁTOVÁNÍ TEXTU ---
    function getFormattedFavoritesText() {
        const favorites = getFavorites();
        if (favorites.length === 0) return null;

        // Převedeme HTML na čistý text a spojíme do jednoho řetězce
        return favorites.map(fav => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = fav.content;
            return tempDiv.textContent || tempDiv.innerText || "";
        }).join('\n\n---\n\n'); // Oddělovač mezi citáty
    }


    // --- FUNKCE PRO VYKRESLENÍ STRÁNKY ---
    function renderFavorites() {
        const favorites = getFavorites();
        container.empty(); 

        if (favorites.length === 0) {
            $('#favorites-actions').hide(); // Skryjeme tlačítka, když nejsou oblíbené
            container.html('<div class="favorites-empty-message"><p>Zatím zde nemáte žádné oblíbené texty.</p><p>Text si můžete přidat kliknutím na hvězdičku u citátu na úvodní stránce.</p></div>');
            return;
        }

        $('#favorites-actions').show(); // Zobrazíme tlačítka
        favorites.forEach(fav => {
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

    function exportFavorites() {
        const text = getFormattedFavoritesText();
        if (!text) {
            alert('Není co exportovat.');
            return;
        }

        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'oblibene_texty.txt';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function copyFavorites() {
        const text = getFormattedFavoritesText();
        if (!text) {
            alert('Není co kopírovat.');
            return;
        }

        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Všechny oblíbené texty byly zkopírovány do schránky.');
            }).catch(err => {
                alert('Nepodařilo se zkopírovat texty. Zkuste to prosím znovu.');
                console.error('Chyba při kopírování: ', err);
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
    
    
    // --- PRVNÍ VYKRESLENÍ PŘI NAČTENÍ STRÁNKY ---
    renderFavorites();

});