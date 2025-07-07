jQuery(document).ready(function($) {
    "use strict";

    /**
     * Formátuje sekundy na formát m:ss (např. 1:05).
     * @param {number} seconds - Celkový počet sekund.
     * @returns {string} Čas ve formátu m:ss.
     */
    function formatTime(seconds) {
        if (isNaN(seconds) || seconds < 0) {
            return '0:00';
        }
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    /**
     * Inicializuje přehrávač pro daný element.
     * @param {jQuery} playerElement - jQuery objekt pro .podcast-player.
     */
    function initializePlayer(playerElement) {
        const audioSrc = playerElement.data('audio-src');
        if (!audioSrc) return;

        const audio = new Audio(audioSrc);
        const playBtn = playerElement.find('.pp-play-pause-btn');
        const playIcon = playBtn.find('i');
        const slider = playerElement.find('.pp-seek-slider');
        const currentTimeEl = playerElement.find('.pp-current-time');
        const durationEl = playerElement.find('.pp-duration');
        
        playerElement.data('audioObject', audio);

        function stopOtherPlayers() {
            $('.podcast-player').each(function() {
                const otherPlayerAudio = $(this).data('audioObject');
                if (otherPlayerAudio && otherPlayerAudio !== audio && !otherPlayerAudio.paused) {
                    otherPlayerAudio.pause();
                }
            });
        }

        playBtn.on('click', function() {
            if (audio.paused) {
                stopOtherPlayers();
                audio.play();
            } else {
                audio.pause();
            }
        });

        audio.addEventListener('play', () => playIcon.removeClass('fa-play').addClass('fa-pause'));
        audio.addEventListener('pause', () => playIcon.removeClass('fa-pause').addClass('fa-play'));
        audio.addEventListener('ended', () => {
            playIcon.removeClass('fa-pause').addClass('fa-play');
            audio.currentTime = 0;
            slider.val(0);
        });
        audio.addEventListener('loadedmetadata', () => {
            durationEl.text(formatTime(audio.duration));
            slider.attr('max', audio.duration);
        });
        audio.addEventListener('timeupdate', () => {
            currentTimeEl.text(formatTime(audio.currentTime));
            if (!isNaN(audio.duration)) slider.val(audio.currentTime);
        });
        slider.on('input', function() {
            if (!isNaN(audio.duration)) audio.currentTime = $(this).val();
        });
    }

    // Inicializace přehrávačů, které jsou na stránce od začátku
    $('.podcast-player').each(function() {
        initializePlayer($(this));
    });

    // Zastaví přehrávání audia, když uživatel opustí stránku
    $(window).on('beforeunload', function() {
        $('.podcast-player').each(function() {
            const audio = $(this).data('audioObject');
            if (audio) {
                audio.pause();
            }
        });
    });

    // --- NOVÁ LOGIKA PRO NAČÍTÁNÍ EPIZOD PO ČÁSTECH ---
    const loadMoreBtn = $('#load-more-podcasts-btn');
    const episodeList = $('.podcast-episode-list');

    if (loadMoreBtn.length && typeof podcast_data !== 'undefined' && podcast_data.remaining_episodes.length > 0) {
        
        let allRemainingEpisodes = podcast_data.remaining_episodes;
        let currentOffset = 0;
        const chunkSize = 7;

        loadMoreBtn.on('click', function() {
            // Získáme další dávku epizod
            const episodeChunk = allRemainingEpisodes.slice(currentOffset, currentOffset + chunkSize);

            if (episodeChunk.length > 0) {
                const episodes_html = episodeChunk.map(episode => {
                    return `
                        <div class="podcast-episode" style="display:none;">
                            <h2 class="episode-title">${escapeHtml(episode.title)}</h2>
                            ${episode.audio_url ? `
                                <div class="podcast-player" data-audio-src="${escapeHtml(episode.audio_url)}">
                                    <div class="pp-top-row">
                                        <button class="pp-play-pause-btn" aria-label="Přehrát / Pauza">
                                            <i class="fa fa-play" aria-hidden="true"></i>
                                        </button>
                                        <div class="pp-time-display">
                                            <span class="pp-current-time">0:00</span>
                                            <span class="pp-time-divider">/</span>
                                            <span class="pp-duration">0:00</span>
                                        </div>
                                    </div>
                                    <div class="pp-slider-wrapper">
                                        <input type="range" class="pp-seek-slider" value="0" min="0" max="100" step="0.1">
                                    </div>
                                </div>
                            ` : ''}
                            <div class="episode-notes">
                                ${episode.description} 
                            </div>
                        </div>
                    `;
                }).join('');

                const newEpisodes = $(episodes_html);
                episodeList.append(newEpisodes);
                
                // Inicializace přehrávačů pro nově přidané epizody
                newEpisodes.find('.podcast-player').each(function() {
                    initializePlayer($(this));
                });

                // Zobrazíme nové epizody s animací
                newEpisodes.slideDown();
                
                // Aktualizujeme pozici pro další načítání
                currentOffset += chunkSize;
                
                // Pokud jsme již načetli všechny, skryjeme tlačítko
                if (currentOffset >= allRemainingEpisodes.length) {
                    $(this).parent().slideUp();
                }
            }
        });
    }

    function escapeHtml(text) {
        if (typeof text !== 'string') return '';
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});