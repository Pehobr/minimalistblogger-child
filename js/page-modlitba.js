/**
 * JavaScript pro stránku s denní modlitbou (/js/page-modlitba.js)
 * VERZE 3: Přidána podpora pro více přehrávačů a archiv.
 */
(function($) {
    $(document).ready(function() {

        /**
         * Inicializuje jeden konkrétní audio přehrávač.
         * @param {jQuery} playerContainer - Kontejner přehrávače (.modlitba-player-container).
         */
        function initializePlayer(playerContainer) {
            if (playerContainer.data('initialized')) {
                return; // Přehrávač je již inicializován
            }

            const audio = playerContainer.find('.modlitba-audio-element')[0];
            const playPauseBtn = playerContainer.find('.modlitba-play-pause-btn');
            const playPauseIcon = playPauseBtn.find('i');
            const progressBar = playerContainer.find('.modlitba-progress-bar');
            const progress = playerContainer.find('.modlitba-progress');
            const currentTimeEl = playerContainer.find('.modlitba-current-time');
            const durationEl = playerContainer.find('.modlitba-duration');

            function formatTime(seconds) {
                if (isNaN(seconds)) return '0:00';
                const minutes = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
            }

            playPauseBtn.on('click', function() {
                if (audio.paused) {
                    // Pozastavíme všechny ostatní přehrávače
                    $('.modlitba-audio-element').each(function() {
                        if (this !== audio) {
                            this.pause();
                        }
                    });
                    audio.play();
                } else {
                    audio.pause();
                }
            });

            $(audio).on('play', function() {
                playPauseIcon.removeClass('fa-play').addClass('fa-pause');
                // Resetujeme ikony ostatních tlačítek
                 $('.modlitba-play-pause-btn').not(playPauseBtn).find('i').removeClass('fa-pause').addClass('fa-play');
            });

            $(audio).on('pause ended', function() {
                playPauseIcon.removeClass('fa-pause').addClass('fa-play');
            });

            $(audio).on('loadedmetadata', function() {
                durationEl.text(formatTime(audio.duration));
            });

            $(audio).on('timeupdate', function() {
                currentTimeEl.text(formatTime(audio.currentTime));
                if (audio.duration) {
                    const progressPercent = (audio.currentTime / audio.duration) * 100;
                    progress.css('width', `${progressPercent}%`);
                }
            });

            progressBar.on('click', function(e) {
                if (!audio.duration) return;
                const barWidth = $(this).width();
                const clickX = e.offsetX;
                audio.currentTime = (clickX / barWidth) * audio.duration;
            });

            playerContainer.data('initialized', true);
        }

        // --- INICIALIZACE HLAVNÍHO PŘEHRÁVAČE ---
        $('.modlitba-player-container[data-is-main-player="true"]').each(function() {
            initializePlayer($(this));
        });


        // --- LOGIKA PRO ARCHIV ---
        const archiveToggleBtn = $('#modlitba-archive-toggle');
        const archiveContainer = $('#past-modlitby-container');

        if (archiveToggleBtn.length && archiveContainer.length) {
            archiveToggleBtn.on('click', function() {
                $(this).toggleClass('active');
                archiveContainer.slideToggle(400, function() {
                    // Po dokončení animace inicializujeme přehrávače v archivu, pokud ještě nejsou
                    if (archiveContainer.is(':visible')) {
                        archiveContainer.find('.modlitba-player-container').each(function() {
                            initializePlayer($(this));
                        });
                    }
                });
            });
        }
    });
})(jQuery);