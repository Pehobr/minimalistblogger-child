jQuery(document).ready(function($) {
    const playerContainer = $('#radio-player-container');
    if (!playerContainer.length) return;

    // Jeden sdílený audio element pro všechna rádia
    const audioPlayer = new Audio();
    audioPlayer.preload = 'none';
    let currentlyPlayingButton = null; // Sledujeme, které TLAČÍTKO je aktivní

    // --- Funkce pro aktualizaci vzhledu ---
    function updateUI(button, isPlaying) {
        // Nejprve resetujeme vzhled všech tlačítek
        $('.radio-play-btn').removeClass('is-playing').find('i').removeClass('fa-pause').addClass('fa-play');
        $('.radio-player-item').removeClass('is-playing');

        // A pak nastavíme vzhled pro aktivní tlačítko, pokud nějaké je
        if (button && isPlaying) {
            button.addClass('is-playing').find('i').removeClass('fa-play').addClass('fa-pause');
            button.closest('.radio-player-item').addClass('is-playing');
        }
    }

    // --- Kliknutí na tlačítko ---
    playerContainer.on('click', '.radio-play-btn', function() {
        const clickedButton = $(this);
        const streamUrl = clickedButton.closest('.radio-player-item').data('stream-url');

        // Pokud klikáme na tlačítko rádia, které právě hraje, tak ho zastavíme.
        if (currentlyPlayingButton && currentlyPlayingButton[0] === clickedButton[0]) {
            audioPlayer.pause();
            currentlyPlayingButton = null;
            updateUI(null, false);
        } else {
            // Jinak spouštíme nové rádio
            audioPlayer.src = streamUrl;
            currentlyPlayingButton = clickedButton; // Nastavíme nové aktivní tlačítko

            const playPromise = audioPlayer.play();

            if (playPromise !== undefined) {
                playPromise.then(_ => {
                    // Přehrávání úspěšně začalo
                    updateUI(clickedButton, true);
                }).catch(error => {
                    // Přehrávání se nepodařilo spustit
                    console.error("Chyba při spouštění rádia:", error);
                    alert("Rádio se nepodařilo spustit. Stream může být dočasně nedostupný.");
                    updateUI(null, false);
                    currentlyPlayingButton = null;
                });
            }
        }
    });

    // Zastavení přehrávání při opuštění stránky
    $(window).on('beforeunload', function() {
        if (audioPlayer) {
            audioPlayer.pause();
        }
    });
});