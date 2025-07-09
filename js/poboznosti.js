/**
 * JavaScript pro stránku "Aplikace Pobožnosti".
 * VERZE 15: Finální oprava funkce individuálních tlačítek u biblických textů.
 */
jQuery(document).ready(function($) {

    // --- 1. KONTROLA DAT A INICIALIZACE ---
    if (typeof poboznostiUdaje === 'undefined' || typeof poboznostiUdaje.audioUrls === 'undefined' || Object.keys(poboznostiUdaje.audioUrls).length === 0) {
        if ($('#poboznosti-player-container').length) {
            $('#poboznosti-player-container').hide();
        }
        return; 
    }

    const audioUrls = poboznostiUdaje.audioUrls;
    const audioElements = {};
    let currentlyPlaying = null;
    let playlist = [];
    let currentTrackIndex = -1;
    let playlistState = 'stopped';

    const titleMap = {
        'audio_1sloka': 'Píseň (kancionál 309) - 1. sloka', 'audio_uvodnimodlitba': 'Úvodní modlitba', 'audio_1cteni': '1. čtení (Jl 2,12–18)', 'audio_zalm': 'Žalm', 'audio_2cteni': '2. čtení (2 Kor 5,20–6,2)', 'audio_2sloka': 'Píseň (kancionál 309) - 2. sloka', 'audio_evang': 'Evangelium (Mt 6,1–6.18)', 'audio_3sloka': 'Píseň (kancionál 309) - 4. sloka', 'audio_impulz': 'Duchovní impulz', 'audio_4sloka': 'Píseň (kancionál 309) - 5. sloka', 'audio_zaverecnamodlitba': 'Závěrečná modlitba', 'audio_5sloka': 'Píseň (kancionál 309) - 6. sloka'
    };
    
    const biblicalKeys = ['audio_1cteni', 'audio_2cteni', 'audio_evang', 'audio_zalm'];
    const poboznostiAppContainer = $('.poboznosti-app');

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

    // --- Robustní metoda klasifikace obsahu a tvorby tlačítek ---
    $('.poboznosti-app .entry-content > *').each(function() {
        const element = $(this);
        let htmlContent = element.html();
        const match = htmlContent.match(/\[AUDIO:([\w_]+)\]/);
        
        if (match && audioUrls[match[1]]) {
            const audioKey = match[1];
            const updatedHtml = htmlContent.replace(match[0], '').trim();
            element.html(updatedHtml).wrapInner('<span class="audio-text-content"></span>').append(`<button class="poboznosti-audio-button" data-audio-key="${audioKey}" aria-label="Přehrát ${titleMap[audioKey] || ''}"><i class="fa fa-headphones"></i></button>`).addClass('has-audio');
            
            const audioElement = new Audio(audioUrls[audioKey]);
            audioElement.preload = 'none';
            audioElements[audioKey] = audioElement;
            audioElement.onended = () => { if (playlistState === 'playing' && playlist[currentTrackIndex] === audioKey) playTrackInPlaylist(currentTrackIndex + 1); else resetIndividualPlay(); };
            audioElement.ontimeupdate = () => { if (playlistState !== 'stopped' && playlist[currentTrackIndex] === audioKey && audioElement.duration) progress.css('width', `${(audioElement.currentTime / audioElement.duration) * 100}%`); };
        }
    });

    // --- Funkce pro ovládání přehrávače ---
    function buildPlaylist() {
        playlist = [];
        const showOnlyBiblical = poboznostiAppContainer.hasClass('biblical-readings-only');
        Object.keys(audioUrls).forEach(key => {
            if (!showOnlyBiblical || (showOnlyBiblical && biblicalKeys.includes(key))) {
                playlist.push(key);
            }
        });
    }
    
    function updateNavButtonsState(index) {
        prevBtn.prop('disabled', index <= 0);
        nextBtn.prop('disabled', index >= playlist.length - 1);
    }

    function resetIndividualPlay() {
        if (currentlyPlaying) currentlyPlaying.element.pause();
        $('.poboznosti-audio-button').removeClass('is-playing is-paused').find('i').removeClass('fa-pause fa-play').addClass('fa-headphones');
        currentlyPlaying = null;
    }

    function resetPlaylist() {
        if (playlistState !== 'stopped' && currentTrackIndex > -1 && playlist.length > 0 && audioElements[playlist[currentTrackIndex]]) {
            audioElements[playlist[currentTrackIndex]].pause();
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
        if (currentTrackIndex > -1 && audioElements[playlist[currentTrackIndex]]) audioElements[playlist[currentTrackIndex]].pause();
        if (index >= playlist.length || index < 0) {
            resetPlaylist();
            resetIndividualPlay();
            return;
        }
        const trackKey = playlist[index];
        if (!audioElements[trackKey]) return;
        currentTrackIndex = index;
        playlistState = 'playing';
        resetIndividualPlay();
        $('.poboznosti-audio-button').removeClass('is-playing is-paused').find('i').removeClass('fa-pause fa-play').addClass('fa-headphones');
        $(`button[data-audio-key="${trackKey}"]`).addClass('is-playing').find('i').removeClass('fa-headphones fa-play').addClass('fa-pause');
        playPauseIcon.removeClass('fa-play').addClass('fa-pause');
        progressBar.removeClass('disabled');
        trackTitle.text(titleMap[trackKey] || '');
        updateNavButtonsState(index);
        audioElements[trackKey].currentTime = 0;
        audioElements[trackKey].play().catch(e => { console.error("Chyba přehrávání:", e); resetPlaylist(); });
    }

    playPauseBtn.on('click', function() {
        resetIndividualPlay();
        if (playlist.length === 0) return;
        const currentAudio = audioElements[playlist[currentTrackIndex]];
        if (playlistState === 'playing' && currentAudio) {
            currentAudio.pause();
            playlistState = 'paused';
            playPauseIcon.removeClass('fa-pause').addClass('fa-play');
            $(`button[data-audio-key="${playlist[currentTrackIndex]}"]`).removeClass('is-playing').addClass('is-paused').find('i').removeClass('fa-pause').addClass('fa-play');
        } else if (playlistState === 'paused' && currentAudio) {
            currentAudio.play();
            playlistState = 'playing';
            playPauseIcon.removeClass('fa-play').addClass('fa-pause');
            $(`button[data-audio-key="${playlist[currentTrackIndex]}"]`).removeClass('is-paused').addClass('is-playing').find('i').removeClass('fa-play').addClass('fa-pause');
        } else {
            playTrackInPlaylist(0);
        }
    });

    prevBtn.on('click', () => { if (playlistState !== 'stopped') playTrackInPlaylist(currentTrackIndex - 1); });
    nextBtn.on('click', () => { if (playlistState !== 'stopped') playTrackInPlaylist(currentTrackIndex + 1); });

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
        if (playlistState === 'stopped' || playlist.length === 0) return;
        const barWidth = $(this).width();
        const clickX = e.offsetX;
        const audio = audioElements[playlist[currentTrackIndex]];
        if (audio && audio.duration) {
            audio.currentTime = (clickX / barWidth) * audio.duration;
        }
    });
    
    // --- 2. SEKCE NASTAVENÍ VZHLEDU ---
    if ($('.poboznosti-app').length) {
        const settingsContent = $('#settings-panel .settings-content');
        
        const playerToggleHTML = `<div class="setting-item" id="toggle-individual-players-container"><label for="toggle-individual-players">Skrýt audio tlačítka u textů</label><label class="switch"><input type="checkbox" id="toggle-individual-players"><span class="slider round"></span></label></div>`;
        const biblicalOnlyHTML = `<div class="setting-item" id="toggle-biblical-only-container"><label for="toggle-biblical-only">Zobrazit pouze biblická čtení</label><label class="switch"><input type="checkbox" id="toggle-biblical-only"><span class="slider round"></span></label></div>`;
        const fontControlsHTML = `<div class="setting-item" id="font-size-controls-container"><label>Velikost písma</label><div class="font-size-controls"><button id="poboznosti-font-decrease" class="font-size-btn" aria-label="Zmenšit písmo">-</button><span id="poboznosti-font-indicator">100%</span><button id="poboznosti-font-increase" class="font-size-btn" aria-label="Zvětšit písmo">+</button></div></div>`;
        
        settingsContent.append(playerToggleHTML,biblicalOnlyHTML,fontControlsHTML);

        const biblicalOnlyToggle = $('#toggle-biblical-only');
        const biblicalOnlyKey = 'poboznosti_showBiblicalOnly';
        
        function applyBiblicalOnlyMode() {
            const shouldShowOnlyBiblical = localStorage.getItem(biblicalOnlyKey) === 'true';
            poboznostiAppContainer.toggleClass('biblical-readings-only', shouldShowOnlyBiblical);
            biblicalOnlyToggle.prop('checked', shouldShowOnlyBiblical);

            let inBiblicalSection = false;
            $('.poboznosti-app .entry-content > *').each(function() {
                const element = $(this);
                if (element.is('h3')) {
                    const headingText = element.text().toLowerCase();
                    const match = element.html().match(/data-audio-key="([\w_]+)"/);
                    inBiblicalSection = (match && biblicalKeys.includes(match[1])) || (!match && (headingText.includes('čtení') || headingText.includes('evangelium') || headingText.includes('žalm')));
                }
                element.toggleClass('non-biblical-content', !inBiblicalSection);
            });

            if (typeof buildPlaylist === 'function') {
                resetPlaylist();
                buildPlaylist();
            }
        }
        
        biblicalOnlyToggle.on('change', function() {
            localStorage.setItem(biblicalOnlyKey, $(this).is(':checked'));
            applyBiblicalOnlyMode();
        });

        const playerToggle = $('#toggle-individual-players');
        const playerVisibilityKey = 'poboznosti_showIndividualPlayers';
        function applyPlayerVisibility() {
            const shouldShow = localStorage.getItem(playerVisibilityKey) !== 'false';
            $('.poboznosti-audio-button').toggle(shouldShow);
            playerToggle.prop('checked', shouldShow);
        }
        playerToggle.on('change', function() {
            localStorage.setItem(playerVisibilityKey, $(this).is(':checked'));
            applyPlayerVisibility();
        });

        const textElements = $('.poboznosti-app .entry-content p');
        const fontSizeStorageKey = 'poboznosti_font_size_rem';
        let currentSizeRem = parseFloat(localStorage.getItem(fontSizeStorageKey)) || 1;
        function applyFontSize(size) {
            const newSize = Math.max(0.7, Math.min(2.0, size));
            textElements.css('font-size', `${newSize}rem`);
            $('#poboznosti-font-indicator').text(`${Math.round(newSize * 100)}%`);
            localStorage.setItem(fontSizeStorageKey, newSize);
            currentSizeRem = newSize;
        }
        $('#poboznosti-font-increase').on('click', () => applyFontSize(currentSizeRem + 0.1));
        $('#poboznosti-font-decrease').on('click', () => applyFontSize(currentSizeRem - 0.1));

        applyBiblicalOnlyMode();
        applyPlayerVisibility();
        applyFontSize(currentSizeRem);
    }
    
    buildPlaylist();
});