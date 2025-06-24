jQuery(document).ready(function($) {
    const playerContainer = $('#radio-player-container');
    if (!playerContainer.length) return;

    // Jeden sdílený audio element pro všechna rádia
    const audioPlayer = new Audio();
    audioPlayer.preload = 'none';
    let currentlyPlayingButton = null;

    // Funkce pro resetování vzhledu VŠECH tlačítek do výchozího stavu
    function resetAllButtons() {
        $('.radio-play-btn').removeClass('is-playing').find('i').removeClass('fa-pause fa-spinner fa-spin').addClass('fa-play');
        $('.radio-player-item').removeClass('is-playing');
    }

    // Kliknutí na tlačítko
    playerContainer.on('click', '.radio-play-btn', function() {
        const clickedButton = $(this);
        const icon = clickedButton.find('i');
        const streamUrl = clickedButton.closest('.radio-player-item').data('stream-url');

        // --- SCÉNÁŘ 1: ZASTAVENÍ PŘEHRÁVÁNÍ ---
        // Pokud klikáme na tlačítko rádia, které právě hraje, tak ho zastavíme.
        if (currentlyPlayingButton && currentlyPlayingButton[0] === clickedButton[0]) {
            audioPlayer.pause();
            resetAllButtons();
            currentlyPlayingButton = null;
            return; // Akce končí
        }

        // --- SCÉNÁŘ 2: SPUŠTĚNÍ NOVÉHO PŘEHRÁVÁNÍ ---

        // KROK A: Okamžitá vizuální zpětná vazba
        resetAllButtons(); // Nejprve resetujeme všechna ostatní tlačítka
        icon.removeClass('fa-play').addClass('fa-spinner fa-spin'); // Na kliknutém tlačítku ukážeme spinner

        // KROK B: Spuštění přehrávání
        audioPlayer.src = streamUrl;
        currentlyPlayingButton = clickedButton; // Uložíme si, které tlačítko je teď aktivní

        const playPromise = audioPlayer.play();

        if (playPromise !== undefined) {
            playPromise.then(_ => {
                // KROK C: Přehrávání úspěšně začalo
                // Změníme spinner na ikonu "Pause"
                icon.removeClass('fa-spinner fa-spin').addClass('fa-pause');
                clickedButton.addClass('is-playing');
                clickedButton.closest('.radio-player-item').addClass('is-playing');

            }).catch(error => {
                // KROK D: Při chybě vše vrátíme do původního stavu
                console.error("Chyba při spouštění rádia:", error);
                alert("Rádio se nepodařilo spustit. Stream může být dočasně nedostupný nebo blokovaný.");
                resetAllButtons();
                currentlyPlayingButton = null;
            });
        }
    });

    // Zastavení přehrávání při opuštění stránky
    $(window).on('beforeunload', function() {
        if (audioPlayer) {
            audioPlayer.pause();
        }
    });
});