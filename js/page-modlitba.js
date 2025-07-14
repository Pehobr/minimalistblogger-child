/**
 * JavaScript pro stránku s denní modlitbou (/js/page-modlitba.js)
 */
(function($) {

    // Spustí se, až bude celá stránka načtená
    $(document).ready(function() {

        // Najdeme audio přehrávač na stránce
        const audioPlayer = document.getElementById('modlitba-player');

        // Zkontrolujeme, jestli přehrávač na stránce existuje
        if (audioPlayer) {
            
            console.log('Přehrávač pro denní modlitbu byl úspěšně inicializován.');

            // Zde je prostor pro budoucí vylepšení:
            // -----------------------------------------

            // Příklad 1: Můžeme poslouchat událost spuštění přehrávání
            audioPlayer.addEventListener('play', function() {
                console.log('Přehrávání modlitby bylo spuštěno.');
                // Zde by se v budoucnu mohl posílat event do Google Analytics apod.
            });

            // Příklad 2: Můžeme poslouchat událost pozastavení
            audioPlayer.addEventListener('pause', function() {
                console.log('Přehrávání modlitby bylo pozastaveno.');
            });
            
            // Příklad 3: Co dělat po skončení přehrávání
            audioPlayer.addEventListener('ended', function() {
                console.log('Přehrávání modlitby dokončeno.');
                // Zde bychom mohli například zobrazit nějaké poděkování nebo tlačítko
            });
        }

    }); // Konec document.ready

})(jQuery);