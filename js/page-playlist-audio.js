jQuery(document).ready(function($) {
    const playerWrapper = $('#custom-audio-player');
    if (!playerWrapper.length) return;

    const playlistId = playerWrapper.data('playlist-id');
    const apiKey = playerWrapper.data('api-key');

    const playPauseBtn = $('#player-play-pause-btn');
    const playIcon = playPauseBtn.find('i');
    const titleEl = $('#player-title');
    const statusEl = $('#player-status');

    let ytPlayer;
    let isPlayerReady = false;
    let isPlaying = false;

    // === ZMĚNA ZDE: Použití správné URL pro načtení YouTube IFrame API ===
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    const firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // Funkce se zavolá, jakmile je API připraveno
    window.onYouTubeIframeAPIReady = function() {
        fetchLatestVideo();
    };

    function fetchLatestVideo() {
        const url = `https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=${playlistId}&maxResults=1&key=${apiKey}`;

        $.getJSON(url, function(data) {
            if (data.items && data.items.length > 0) {
                const latestVideo = data.items[0].snippet;
                const videoId = latestVideo.resourceId.videoId;
                const videoTitle = latestVideo.title;

                titleEl.text(videoTitle);
                statusEl.text('Připraveno k přehrání');
                playerWrapper.removeClass('loading');
                
                createPlayer(videoId);
            } else {
                titleEl.text('Chyba');
                statusEl.text('Nepodařilo se načíst video z playlistu.');
            }
        }).fail(function() {
            titleEl.text('Chyba API');
            statusEl.text('Zkontrolujte prosím API klíč nebo ID playlistu.');
        });
    }

    function createPlayer(videoId) {
        ytPlayer = new YT.Player('youtube-player-container', {
            height: '0',
            width: '0',
            videoId: videoId,
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }

    function onPlayerReady(event) {
        isPlayerReady = true;
    }

    function onPlayerStateChange(event) {
        if (event.data === YT.PlayerState.PLAYING) {
            isPlaying = true;
            playIcon.removeClass('fa-play').addClass('fa-pause');
            statusEl.text('Přehrává se...');
        } else {
            isPlaying = false;
            playIcon.removeClass('fa-pause').addClass('fa-play');
             if (event.data === YT.PlayerState.PAUSED) {
                statusEl.text('Pozastaveno');
            } else if (event.data === YT.PlayerState.ENDED) {
                statusEl.text('Přehráno do konce');
            } else {
                statusEl.text('Připraveno k přehrání');
            }
        }
    }

    playPauseBtn.on('click', function() {
        if (!isPlayerReady) return;

        if (isPlaying) {
            ytPlayer.pauseVideo();
        } else {
            ytPlayer.playVideo();
        }
    });
});