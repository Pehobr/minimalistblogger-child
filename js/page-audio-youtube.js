jQuery(document).ready(function($) {
    // Globální objekty pro ukládání přehrávačů a jejich stavů
    window.ytPlayers = {};
    window.ytPlayerStates = {};
    window.ytPublishingDates = {};

    // Tato funkce se zavolá automaticky, až bude YouTube API připravené
    window.onYouTubeIframeAPIReady = function() {
        // Projdeme všechny audio přehrávače na stránce a inicializujeme je
        $('.custom-audio-player').each(function() {
            fetchLatestVideo($(this));
        });
    };

    // === OPRAVA ZDE: Použití oficiální a spolehlivé URL pro načtení YouTube API ===
    if (!$('script[src="https://www.youtube.com/iframe_api"]').length) {
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

    // Pomocná funkce pro hezčí formát data
    function formatYouTubeDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('cs-CZ', { day: 'numeric', month: 'numeric', year: 'numeric' });
    }

    // Funkce pro načtení dat o posledním videu z playlistu
    function fetchLatestVideo(playerWrapper) {
        const uniqueId = playerWrapper.attr('id');
        const playlistId = playerWrapper.data('playlist-id');
        const apiKey = playerWrapper.data('api-key');
        const titleEl = playerWrapper.find('.player-video-title');
        const statusEl = playerWrapper.find('.player-status');

        if (!playlistId || !apiKey) {
            titleEl.text('Chyba konfigurace');
            statusEl.text('Chybí ID playlistu nebo API klíč.');
            playerWrapper.removeClass('loading');
            return;
        }

        const url = `https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=${playlistId}&maxResults=1&key=${apiKey}`;

        $.getJSON(url, function(data) {
            if (data.items && data.items.length > 0) {
                const latestVideo = data.items[0].snippet;
                const videoId = latestVideo.resourceId.videoId;
                
                window.ytPublishingDates[uniqueId] = formatYouTubeDate(latestVideo.publishedAt);
                
                titleEl.text(latestVideo.title);
                statusEl.text(`Publikováno: ${window.ytPublishingDates[uniqueId]}`);
                playerWrapper.removeClass('loading');
                createPlayer(playerWrapper, videoId);
            } else {
                titleEl.text('Žádné video');
                statusEl.text('V playlistu nebylo nalezeno žádné video.');
                playerWrapper.removeClass('loading');
            }
        }).fail(function() {
            titleEl.text('Chyba API');
            statusEl.text('Nepodařilo se načíst data z YouTube.');
            playerWrapper.removeClass('loading');
        });
    }

    // Funkce pro vytvoření skrytého YouTube přehrávače
    function createPlayer(playerWrapper, videoId) {
        const uniqueId = playerWrapper.attr('id');
        const youtubeContainerId = uniqueId.replace('audio-player-', 'youtube-player-container-');

        window.ytPlayers[uniqueId] = new YT.Player(youtubeContainerId, {
            height: '0', width: '0', videoId: videoId,
            playerVars: { 'playsinline': 1 },
            events: {
                'onReady': (event) => onPlayerReady(event, uniqueId),
                'onStateChange': (event) => onPlayerStateChange(event, uniqueId)
            }
        });
    }

    function onPlayerReady(event, uniqueId) {
        window.ytPlayerStates[uniqueId] = { isReady: true, isPlaying: false };
    }
    
    // Klíčová funkce, která reaguje na změny stavu přehrávače (hraje, pauza, atd.)
    function onPlayerStateChange(event, uniqueId) {
        const playerWrapper = $('#' + uniqueId);
        const playIcon = playerWrapper.find('.player-play-pause-btn i');
        const statusEl = playerWrapper.find('.player-status');

        // Když se video začne přehrávat
        if (event.data === YT.PlayerState.PLAYING) {
            window.ytPlayerStates[uniqueId].isPlaying = true;
            playIcon.removeClass('fa-play').addClass('fa-pause');
            statusEl.text('Přehrává se...');
            playerWrapper.addClass('is-playing');

            // Projdeme všechny ostatní přehrávače a pozastavíme je, POKUD právě hrají
            for (const id in window.ytPlayers) {
                if (id !== uniqueId && window.ytPlayers[id] && typeof window.ytPlayers[id].pauseVideo === 'function') {
                    if (window.ytPlayers[id].getPlayerState() === YT.PlayerState.PLAYING) {
                       window.ytPlayers[id].pauseVideo();
                    }
                }
            }
        // Když se video zastaví (pauza, konec, atd.)
        } else {
            if (window.ytPlayerStates[uniqueId]) { // Pojistka, aby se kód nespustil příliš brzy
                window.ytPlayerStates[uniqueId].isPlaying = false;
            }
            playIcon.removeClass('fa-pause').addClass('fa-play');
            playerWrapper.removeClass('is-playing');
            
            // Zobrazíme konkrétní stav
            if (event.data === YT.PlayerState.PAUSED) {
                statusEl.text('Pozastaveno');
            } else if (event.data === YT.PlayerState.ENDED) {
                statusEl.text('Přehráno do konce');
            } else { // Pro ostatní stavy (včetně "připraveno")
                if(window.ytPublishingDates[uniqueId]) {
                    statusEl.text(`Publikováno: ${window.ytPublishingDates[uniqueId]}`);
                 } else {
                    statusEl.text('Připraveno');
                 }
            }
        }
    }

    // Kliknutí na naše tlačítko play/pause
    $('.player-play-pause-btn').on('click', function() {
        const playerWrapper = $(this).closest('.custom-audio-player');
        const uniqueId = playerWrapper.attr('id');

        if (window.ytPlayerStates[uniqueId] && window.ytPlayerStates[uniqueId].isReady) {
            if (window.ytPlayerStates[uniqueId].isPlaying) {
                window.ytPlayers[uniqueId].pauseVideo();
            } else {
                window.ytPlayers[uniqueId].playVideo();
            }
        }
    });
});