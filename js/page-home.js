/**
 * JavaScript pro úvodní stránku (page-home)
 * Zajišťuje funkčnost modálního okna pro zobrazení citátů.
 */
jQuery(document).ready(function($) {

    // 1. Selekce všech potřebných prvků
    const gridItems = $('.icon-grid-item');
    const modalOverlay = $('#quote-modal-overlay');
    const modalContainer = $('#quote-modal-container');
    const modalContent = $('#quote-modal-content');
    const closeModalBtn = $('#quote-modal-close-btn');

    // 2. Funkce pro otevření a zavření modálního okna
    function openModal(quoteHtml) {
        // <<<=== ZMĚNA ZDE: Používáme .html() místo .text() ===>>>
        modalContent.html(quoteHtml);
        modalOverlay.fadeIn(200);
        modalContainer.fadeIn(300);
        modalContainer.addClass('is-visible');
    }

    function closeModal() {
        modalOverlay.fadeOut(300);
        modalContainer.fadeOut(200, function() {
             modalContainer.removeClass('is-visible');
        });
    }

    // 3. Přidání posluchače událostí na kliknutí na dlaždice
    gridItems.on('click', function(event) {
        // <<<=== ZMĚNA ZDE: Čteme nový 'data-target-id' atribut ===>>>
        const targetId = $(this).data('target-id');

        // Pokud má dlaždice tento atribut (tedy má přiřazený citát)
        if (targetId) {
            event.preventDefault(); // Zabráníme výchozí akci odkazu (přechod na #)
            
            // Najdeme skrytý div podle jeho ID a získáme jeho HTML obsah
            const quoteHtmlContent = $('#' + targetId).html();

            if (quoteHtmlContent) {
                openModal(quoteHtmlContent);
            }
        }
        // Pokud atribut nemá, skript nic neudělá a odkaz se normálně otevře.
    });

    // 4. Přidání posluchačů pro zavření okna
    closeModalBtn.on('click', closeModal); // Klik na zavírací tlačítko
    modalOverlay.on('click', closeModal);  // Klik na pozadí (overlay)

    // Bonus: Zavření okna klávesou Escape
    $(document).on('keydown', function(event) {
        if (event.key === "Escape" && modalContainer.hasClass('is-visible')) {
            closeModal();
        }
    });

});