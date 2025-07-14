/**
 * JavaScript pro stránku s denní modlitbou (/js/page-modlitba.js)
 * VERZE 2: Logika pro vlastní audio přehrávač.
 */
(function($) {
    $(document).ready(function() {

        const playerContainer = $('#modlitba-player-container');
        if (!playerContainer.length) {
            return;
        }

        // Selekce prvků přehrávače
        const audio = $('#modlitba-audio-element')[0];
        const playPauseBtn = $('#modlitba-play-pause-btn');
        const playPauseIcon = playPauseBtn.find('i');
        const progressBar = $('#modlitba-progress-bar');
        const progress = $('#modlitba-progress');
        const currentTimeEl = $('#modlitba-current-time');
        const durationEl = $('#modlitba-duration');

        // Funkce pro formátování času (např. 125 sekund -> "2:05")
        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const minutes = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
        }

        // Kliknutí na tlačítko Play/Pause
        playPauseBtn.on('click', function() {
            if (audio.paused) {
                audio.play();
            } else {
                audio.pause();
            }
        });

        // Změna ikony při přehrávání / pauze
        $(audio).on('play', function() {
            playPauseIcon.removeClass('fa-play').addClass('fa-pause');
        });
        $(audio).on('pause', function() {
            playPauseIcon.removeClass('fa-pause').addClass('fa-play');
        });

        // Po načtení metadat audia (délka atd.)
        $(audio).on('loadedmetadata', function() {
            durationEl.text(formatTime(audio.duration));
        });

        // Při přehrávání aktualizujeme progress bar a čas
        $(audio).on('timeupdate', function() {
            // Aktualizace času
            currentTimeEl.text(formatTime(audio.currentTime));
            
            // Aktualizace progress baru
            if (audio.duration) {
                const progressPercent = (audio.currentTime / audio.duration) * 100;
                progress.css('width', `${progressPercent}%`);
            }
        });

        // Možnost posouvat přehrávání kliknutím na progress bar
        progressBar.on('click', function(e) {
            if (!audio.duration) return;
            const barWidth = $(this).width();
            const clickX = e.offsetX;
            audio.currentTime = (clickX / barWidth) * audio.duration;
        });

    });
})(jQuery);