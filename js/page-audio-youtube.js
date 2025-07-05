jQuery(document).ready(function($) {
    window.ytPlayers = [];
    window.ytPlayerStates = [];
    // Uložíme si data publikování pro pozdější použití
    window.ytPublishingDates = [];

    window.onYouTubeIframeAPIReady = function() {
        $('.custom-audio-player').each(function(index) {
            fetchLatestVideo(index);
        });
    };

    if (!$('script[src="https://www.youtube.com/iframe_api"]').length) {
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

    // Pomocná funkce pro formátování data
    function formatYouTubeDate(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Měsíce jsou od 0
        const year = date.getFullYear();
        return `${day}. ${month}. ${year}`;
    }

    function fetchLatestVideo(index) {
        const playerWrapper = $('#audio-player-' + index);
        const playlistId = playerWrapper.data('playlist-id');
        const apiKey = playerWrapper.data('api-key');
        const titleEl = playerWrapper.find('.player-video-title');
        const statusEl = playerWrapper.find('.player-status');

        if (!playlistId || !apiKey || apiKey === 'YOUR_YOUTUBE_API_KEY') {
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
                const videoTitle = latestVideo.title;

                // === ZMĚNA ZDE: Uložíme si naformátované datum publikování ===
                window.ytPublishingDates[index] = formatYouTubeDate(latestVideo.publishedAt);

                titleEl.text(videoTitle);
                // === ZMĚNA ZDE: Zobrazíme datum publikování ===
                statusEl.text(`Publikováno: ${window.ytPublishingDates[index]}`);
                playerWrapper.removeClass('loading');
                createPlayer(index, videoId);
            } else {
                titleEl.text('Žádné video');
                statusEl.text('V playlistu nebylo nalezeno žádné video.');
                playerWrapper.removeClass('loading');
            }
        }).fail(function(jqxhr, textStatus, error) {
            let errorMsg = 'Chyba při načítání dat z YouTube.';
            if(jqxhr.responseJSON && jqxhr.responseJSON.error && jqxhr.responseJSON.error.message) {
               errorMsg = jqxhr.responseJSON.error.message;
            }
            titleEl.text('Chyba API');
            statusEl.text(errorMsg);
            playerWrapper.removeClass('loading');
        });
    }

    function createPlayer(index, videoId) {
        window.ytPlayers[index] = new YT.Player('youtube-player-container-' + index, {
            height: '0',
            width: '0',
            videoId: videoId,
            playerVars: {
                'playsinline': 1
            },
            events: {
                'onReady': (event) => onPlayerReady(event, index),
                'onStateChange': (event) => onPlayerStateChange(event, index)
            }
        });
    }

    function onPlayerReady(event, index) {
        window.ytPlayerStates[index] = { isReady: true, isPlaying: false };
    }

    function onPlayerStateChange(event, index) {
        const playerWrapper = $('#audio-player-' + index);
        const playIcon = playerWrapper.find('.player-play-pause-btn i');
        const statusEl = playerWrapper.find('.player-status');

        if (event.data === YT.PlayerState.PLAYING) {
            window.ytPlayerStates[index].isPlaying = true;
            playIcon.removeClass('fa-play').addClass('fa-pause');
            statusEl.text('Přehrává se...');
            playerWrapper.addClass('is-playing');
            // Zastavíme ostatní přehrávače
            for (let i = 0; i < window.ytPlayers.length; i++) {
                if (i !== index && window.ytPlayers[i] && typeof window.ytPlayers[i].pauseVideo === 'function') {
                    window.ytPlayers[i].pauseVideo();
                }
            }
        } else {
            window.ytPlayerStates[index].isPlaying = false;
            playIcon.removeClass('fa-pause').addClass('fa-play');
            playerWrapper.removeClass('is-playing');
            
            // === ZMĚNA ZDE: Zobrazíme datum, když se audio nehraje ===
            if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED || event.data === YT.PlayerState.UNSTARTED) {
                 statusEl.text(`Publikováno: ${window.ytPublishingDates[index]}`);
            }
        }
    }

    $('.player-play-pause-btn').on('click', function() {
        const playerWrapper = $(this).closest('.custom-audio-player');
        const index = parseInt(playerWrapper.attr('id').split('-')[2]);

        if (window.ytPlayerStates[index] && window.ytPlayerStates[index].isReady) {
            if (window.ytPlayerStates[index].isPlaying) {
                window.ytPlayers[index].pauseVideo();
            } else {
                window.ytPlayers[index].playVideo();
            }
        }
    });
});