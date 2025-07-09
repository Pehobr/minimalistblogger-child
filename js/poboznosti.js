/**
 * JavaScript pro stránku "Aplikace Pobožnosti".
 * VERZE 10: Opraveno skrývání celých nebiblických sekcí (nadpis + obsah).
 */
jQuery(document).ready(function($) {

    // --- 1. KONTROLA DAT A INICIALIZACE AUDIO PŘEHRÁVAČE ---
    if (typeof poboznostiUdaje !== 'undefined' && typeof poboznostiUdaje.audioUrls !== 'undefined' && Object.keys(poboznostiUdaje.audioUrls).length > 0) {
        
        const audioUrls = poboznostiUdaje.audioUrls;
        const audioElements = {};
        let currentlyPlaying = null;
        const playlist = [];
        let currentTrackIndex = -1;
        let playlistState = 'stopped';

        const titleMap = {
            'audio_1sloka': 'Píseň (kancionál 309) - 1. sloka',
            'audio_uvodnimodlitba': 'Úvodní modlitba',
            'audio_1cteni': '1. čtení (Jl 2,12–18)',
            'audio_zalm': 'Žalm',
            'audio_2cteni': '2. čtení (2 Kor 5,20–6,2)',
            'audio_2sloka': 'Píseň (kancionál 309) - 2. sloka',
            'audio_evang': 'Evangelium (Mt 6,1–6.16–18)',
            'audio_3sloka': 'Píseň (kancionál 309) - 4. sloka',
            'audio_impulz': 'Duchovní impulz',
            'audio_4sloka': 'Píseň (kancionál 309) - 5. sloka',
            'audio_zaverecnamodlitba': 'Závěrečná modlitba',
            'audio_5sloka': 'Píseň (kancionál 309) - 6. sloka'
        };

        const playPauseBtn = $('#poboznosti-play-pause-btn');
        const playPauseIcon = playPauseBtn.find('i');
        const prevBtn = $('#poboznosti-prev-btn');
        const nextBtn = $('#poboznosti-next-btn');
        const progressBar = $('#poboznosti-progress-bar');
        const progress = $('#poboznosti-progress');
        const trackTitle = $('#poboznosti-track-title');

        progressBar.addClass('disabled');
        prevBtn.prop('disabled', true);
        nextBtn.prop('disabled', true);
        
        // --- KLÍČOVÁ ZMĚNA V IDENTIFIKACI OBSAHU ---
        const biblicalKeys = ['audio_1cteni', 'audio_2cteni', 'audio_evang'];
        
        // Nejprve projdeme všechny H3, abychom označili celé sekce
        $('.poboznosti-app .entry-content h3').each(function() {
            const heading = $(this);
            const match = heading.html().match(/\[AUDIO:([\w_]+)\]/);
            
            if (match) {
                const audioKey = match[1];
                if (!biblicalKeys.includes(audioKey)) {
                    // Označíme nadpis a všechny následující elementy až do dalšího H3
                    heading.addClass('non-biblical-content');
                    heading.nextUntil('h3').addClass('non-biblical-content');
                }
            }
        });

        // Poté projdeme všechny relevantní elementy a přidáme tlačítka
        $('.poboznosti-app .entry-content p, .poboznosti-app .entry-content h3').each(function() {
            const element = $(this);
            let element_html = element.html();
            const match = element_html.match(/\[AUDIO:([\w_]+)\]/);

            if (match) {
                const placeholder = match[0];
                const audioKey = match[1];

                if (audioUrls[audioKey]) {
                    element_html = element_html.replace(placeholder, '').trim();
                    element.html(element_html);
                    const button = $(`<button class="poboznosti-audio-button" data-audio-key="${audioKey}" aria-label="Přehrát ${titleMap[audioKey] || audioKey}"><i class="fa fa-headphones"></i></button>`);
                    element.wrapInner('<span class="audio-text-content"></span>').append(button);
                    element.addClass('has-audio');

                    const audioElement = new Audio(audioUrls[audioKey]);
                    audioElement.preload = 'none';
                    audioElements[audioKey] = audioElement;
                    
                    audioElement.onended = function() {
                        if (playlistState === 'playing' && playlist[currentTrackIndex] === audioKey) {
                            playTrackInPlaylist(currentTrackIndex + 1);
                        } else {
                            resetIndividualPlay();
                        }
                    };

                    audioElement.ontimeupdate = function() {
                        if (playlistState !== 'stopped' && playlist[currentTrackIndex] === audioKey && this.duration) {
                            const progressPercent = (this.currentTime / this.duration) * 100;
                            progress.css('width', progressPercent + '%');
                        }
                    };
                }
            }
        });

        for (const key in audioUrls) {
            if (Object.prototype.hasOwnProperty.call(audioUrls, key)) {
                playlist.push(key);
            }
        }

        function updateNavButtonsState(index) {
            prevBtn.prop('disabled', index <= 0);
            nextBtn.prop('disabled', index >= playlist.length - 1);
        }

        function resetIndividualPlay() {
            if (currentlyPlaying && currentlyPlaying.element) {
                currentlyPlaying.element.pause();
            }
            $('.poboznosti-audio-button').removeClass('is-playing is-paused').find('i').removeClass('fa-pause fa-play').addClass('fa-headphones');
            currentlyPlaying = null;
        }

        function resetPlaylist() {
            if (playlistState !== 'stopped' && currentTrackIndex > -1) {
                const currentAudioKey = playlist[currentTrackIndex];
                if (audioElements[currentAudioKey]) {
                    audioElements[currentAudioKey].pause();
                }
            }
            playlistState = 'stopped';
            currentTrackIndex = -1;
            playPauseIcon.removeClass('fa-pause').addClass('fa-play');
            progress.css('width', '0%');
            progressBar.addClass('disabled');
            trackTitle.text('');
            updateNavButtonsState(-1);
        }

        function playTrackInPlaylist(index) {
            if (currentTrackIndex > -1 && playlist[currentTrackIndex] && audioElements[playlist[currentTrackIndex]]) {
                audioElements[playlist[currentTrackIndex]].pause();
            }

            if (index >= playlist.length || index < 0) {
                resetPlaylist();
                resetIndividualPlay();
                return;
            }

            const trackKey = playlist[index];
            const audioElement = audioElements[trackKey];
            if (!audioElement) return;

            currentTrackIndex = index;
            playlistState = 'playing';
            
            resetIndividualPlay();
            
            $('.poboznosti-audio-button').removeClass('is-playing is-paused').find('i').removeClass('fa-pause fa-play').addClass('fa-headphones');
            $(`button[data-audio-key="${trackKey}"]`).addClass('is-playing').find('i').removeClass('fa-headphones fa-play').addClass('fa-pause');

            playPauseIcon.removeClass('fa-play').addClass('fa-pause');
            progressBar.removeClass('disabled');
            trackTitle.text(titleMap[trackKey] || '');
            updateNavButtonsState(index);

            audioElement.currentTime = 0;
            audioElement.play().catch(function(error) {
                console.error("Chyba při přehrávání:", error);
                resetPlaylist();
            });
        }

        playPauseBtn.on('click', function() {
            resetIndividualPlay();
            const currentAudioKey = playlist[currentTrackIndex];
            const currentAudioElement = audioElements[currentAudioKey];

            if (playlistState === 'playing' && currentAudioElement) {
                currentAudioElement.pause();
                playlistState = 'paused';
                playPauseIcon.removeClass('fa-pause').addClass('fa-play');
                $(`button[data-audio-key="${currentAudioKey}"]`).removeClass('is-playing').addClass('is-paused').find('i').removeClass('fa-pause').addClass('fa-play');
            } else if (playlistState === 'paused' && currentAudioElement) {
                currentAudioElement.play();
                playlistState = 'playing';
                playPauseIcon.removeClass('fa-play').addClass('fa-pause');
                $(`button[data-audio-key="${currentAudioKey}"]`).removeClass('is-paused').addClass('is-playing').find('i').removeClass('fa-play').addClass('fa-pause');
            } else {
                playTrackInPlaylist(0);
            }
        });

        prevBtn.on('click', function() {
            if (playlistState !== 'stopped') playTrackInPlaylist(currentTrackIndex - 1);
        });

        nextBtn.on('click', function() {
            if (playlistState !== 'stopped') playTrackInPlaylist(currentTrackIndex + 1);
        });

        $(document).on('click', '.poboznosti-audio-button', function(e) {
            e.stopPropagation();
            const clickedKey = $(this).data('audio-key');
            if (playlistState !== 'stopped') resetPlaylist();

            if (currentlyPlaying && currentlyPlaying.key === clickedKey) {
                currentlyPlaying.element.pause();
                $(this).removeClass('is-playing').addClass('is-paused').find('i').removeClass('fa-pause').addClass('fa-play');
                currentlyPlaying = null;
            } else {
                resetIndividualPlay();
                if (audioElements[clickedKey]) {
                    currentlyPlaying = { key: clickedKey, element: audioElements[clickedKey] };
                    currentlyPlaying.element.currentTime = 0;
                    currentlyPlaying.element.play();
                    $(this).removeClass('is-paused').addClass('is-playing').find('i').removeClass('fa-headphones fa-play').addClass('fa-pause');
                }
            }
        });

        progressBar.on('click', function(e) {
            if (playlistState === 'stopped') return;
            const barWidth = $(this).width();
            const clickX = e.offsetX;
            const audioElement = audioElements[playlist[currentTrackIndex]];
            if (audioElement && audioElement.duration) {
                audioElement.currentTime = (clickX / barWidth) * audioElement.duration;
            }
        });

    } else {
        if ($('#poboznosti-player-container').length) {
            $('#poboznosti-player-container').hide();
        }
    }

    // --- 2. SEKCE NASTAVENÍ VZHLEDU ---
    if ($('.poboznosti-app').length) {
        
        const settingsContent = $('#settings-panel .settings-content');

        // --- Nastavení velikosti písma ---
        const fontControlsHTML = `<div class="setting-item" id="font-size-controls-container"><label>Velikost písma</label><div class="font-size-controls"><button id="poboznosti-font-decrease" class="font-size-btn" aria-label="Zmenšit písmo">-</button><span id="poboznosti-font-indicator">100%</span><button id="poboznosti-font-increase" class="font-size-btn" aria-label="Zvětšit písmo">+</button></div></div>`;
        settingsContent.append(fontControlsHTML);

        const textElements = $('.poboznosti-app .entry-content p');
        const fontSizeStorageKey = 'poboznosti_font_size_rem';
        const baseFontSize = 1; 
        let currentSizeRem = parseFloat(localStorage.getItem(fontSizeStorageKey)) || baseFontSize;

        function applyFontSize(sizeInRem) {
            const newSizeRem = Math.max(0.7, Math.min(2.0, sizeInRem));
            textElements.css('font-size', newSizeRem + 'rem');
            const percentage = Math.round((newSizeRem / baseFontSize) * 100);
            $('#poboznosti-font-indicator').text(percentage + '%');
            localStorage.setItem(fontSizeStorageKey, newSizeRem);
            currentSizeRem = newSizeRem;
        }

        $('#poboznosti-font-increase').on('click', () => applyFontSize(currentSizeRem + 0.1));
        $('#poboznosti-font-decrease').on('click', () => applyFontSize(currentSizeRem - 0.1));
        applyFontSize(currentSizeRem);

        // --- Nastavení viditelnosti tlačítek přehrávačů ---
        const individualPlayerControlsHTML = `<div class="setting-item" id="toggle-individual-players-container"><label for="toggle-individual-players">Zobrazit tlačítka u textů</label><label class="switch"><input type="checkbox" id="toggle-individual-players"><span class="slider round"></span></label></div>`;
        settingsContent.append(individualPlayerControlsHTML);
        
        const individualPlayerToggle = $('#toggle-individual-players');
        const playerVisibilityKey = 'poboznosti_showIndividualPlayers';

        function applyPlayerVisibility() {
            const shouldShow = localStorage.getItem(playerVisibilityKey);
            if (shouldShow === 'false') {
                $('.poboznosti-audio-button').hide();
                individualPlayerToggle.prop('checked', false);
            } else {
                $('.poboznosti-audio-button').show();
                individualPlayerToggle.prop('checked', true);
            }
        }

        individualPlayerToggle.on('change', function() {
            if ($(this).is(':checked')) {
                $('.poboznosti-audio-button').fadeIn(200);
                localStorage.setItem(playerVisibilityKey, 'true');
            } else {
                $('.poboznosti-audio-button').fadeOut(200);
                localStorage.setItem(playerVisibilityKey, 'false');
            }
        });
        applyPlayerVisibility();

        // --- Zobrazit pouze biblická čtení ---
        const biblicalOnlyHTML = `<div class="setting-item" id="toggle-biblical-only-container"><label for="toggle-biblical-only">Zobrazit pouze biblická čtení</label><label class="switch"><input type="checkbox" id="toggle-biblical-only"><span class="slider round"></span></label></div>`;
        settingsContent.append(biblicalOnlyHTML);

        const biblicalOnlyToggle = $('#toggle-biblical-only');
        const poboznostiAppContainer = $('.poboznosti-app');
        const biblicalOnlyKey = 'poboznosti_showBiblicalOnly';
        
        function applyBiblicalOnlyMode() {
            const shouldShowOnlyBiblical = localStorage.getItem(biblicalOnlyKey) === 'true';
            if (shouldShowOnlyBiblical) {
                poboznostiAppContainer.addClass('biblical-readings-only');
                biblicalOnlyToggle.prop('checked', true);
            } else {
                poboznostiAppContainer.removeClass('biblical-readings-only');
                biblicalOnlyToggle.prop('checked', false);
            }
        }
        
        biblicalOnlyToggle.on('change', function() {
            localStorage.setItem(biblicalOnlyKey, $(this).is(':checked'));
            applyBiblicalOnlyMode();
        });

        applyBiblicalOnlyMode();
    }
});