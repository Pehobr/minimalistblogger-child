jQuery(document).ready(function($) {
    // Najdeme tlačítko zpět
    const backButton = $('.back-to-home-btn');

    // Počkej chvíli po načtení stránky a spusť animaci
    setTimeout(function() {
        backButton.addClass('expanded');
    }, 500); // 0.5s zpoždění

    // Po 3.5 sekundách (0.5s zpoždění + 3s zobrazení) vrať tlačítko do původního stavu
    setTimeout(function() {
        backButton.removeClass('expanded');
    }, 3500);
});