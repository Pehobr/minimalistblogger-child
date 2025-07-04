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

    // Projde každý přehrávač na stránce a inicializuje ho
    $('.podcast-player').each(function() {
        const playerElement = $(this);
        const audioSrc = playerElement.data('audio-src');
        if (!audioSrc) return;

        const audio = new Audio(audioSrc);
        const playBtn = playerElement.find('.pp-play-pause-btn');
        const playIcon = playBtn.find('i');
        const slider = playerElement.find('.pp-seek-slider');
        const currentTimeEl = playerElement.find('.pp-current-time');
        const durationEl = playerElement.find('.pp-duration');

        // Funkce pro zastavení všech ostatních přehrávačů
        function stopOtherPlayers() {
            $('audio').each(function() {
                if (this !== audio && !this.paused) {
                    this.pause();
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

        audio.addEventListener('play', () => {
            playIcon.removeClass('fa-play').addClass('fa-pause');
        });

        audio.addEventListener('pause', () => {
            playIcon.removeClass('fa-pause').addClass('fa-play');
        });
        
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
            if (!isNaN(audio.duration)) {
                slider.val(audio.currentTime);
            }
        });
        
        slider.on('input', function() {
            if (!isNaN(audio.duration)) {
                audio.currentTime = $(this).val();
            }
        });
    });

    // Zastaví přehrávání audia, když uživatel opustí stránku
    $(window).on('beforeunload', function() {
        $('audio').each(function() {
            this.pause();
        });
    });
});