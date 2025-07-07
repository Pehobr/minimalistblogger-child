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

    // Načtení skriptu pro YouTube IFrame API, pokud ještě není načten
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
        const youtubeContainerId = 'youtube-player-container-' + uniqueId.replace('audio-player-', '');

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
    
    // Klíčová funkce, která reaguje na změny stavu přehrávače
    function onPlayerStateChange(event, uniqueId) {
        const playerWrapper = $('#' + uniqueId.replace(/ /g, '\\ ')); // Oprava pro ID s mezerami
        const playIcon = playerWrapper.find('.player-play-pause-btn i');
        const statusEl = playerWrapper.find('.player-status');
        const defaultStatusText = `Publikováno: ${window.ytPublishingDates[uniqueId] || 'Připraveno'}`;

        // Přesně reagujeme na konkrétní stav
        if (event.data === YT.PlayerState.PLAYING) {
            window.ytPlayerStates[uniqueId].isPlaying = true;
            playIcon.removeClass('fa-play').addClass('fa-pause');
            statusEl.text('Přehrává se...');
            playerWrapper.addClass('is-playing');

            // Pozastavíme všechny ostatní přehrávače
            for (const id in window.ytPlayers) {
                if (id !== uniqueId && window.ytPlayers[id] && typeof window.ytPlayers[id].getPlayerState === 'function' && window.ytPlayers[id].getPlayerState() === YT.PlayerState.PLAYING) {
                   window.ytPlayers[id].pauseVideo();
                }
            }
        } else if (event.data === YT.PlayerState.PAUSED) {
            window.ytPlayerStates[uniqueId].isPlaying = false;
            playIcon.removeClass('fa-pause').addClass('fa-play');
            statusEl.text('Pozastaveno');
            playerWrapper.removeClass('is-playing');
        } else if (event.data === YT.PlayerState.ENDED) {
            window.ytPlayerStates[uniqueId].isPlaying = false;
            playIcon.removeClass('fa-pause').addClass('fa-play');
            statusEl.text('Přehráno do konce');
            playerWrapper.removeClass('is-playing');
        }
    }

    // Kliknutí na naše tlačítko play/pause
    $(document).on('click', '.player-play-pause-btn', function() {
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