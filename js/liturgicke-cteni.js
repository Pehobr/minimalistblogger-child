/**
 * JavaScript pro stránku "Aplikace Liturgické Čtení" a její příspěvky.
 * VERZE 8: Robustnější detekce nadpisů pro přidání ikon.
 */
jQuery(document).ready(function($) {

    // --- 1. KONTROLA DAT A INICIALIZACE ---
    if (typeof liturgickeUdaje === 'undefined' || typeof liturgickeUdaje.audioUrls === 'undefined' || Object.keys(liturgickeUdaje.audioUrls).length === 0) {
        $('#playlist-player-container').hide();
        return; 
    }

    const audioUrls = liturgickeUdaje.audioUrls;
    const audioElements = {};
    let currentlyPlaying = null;

    const playlist = [];
    let currentTrackIndex = -1;
    let playlistState = 'stopped';

    const titleMap = { 'cteni_1': '1. čtení', 'cteni_2': '2. čtení', 'evangelium': 'Evangelium' };

    const playPauseBtn = $('#playlist-play-pause-btn');
    const playPauseIcon = playPauseBtn.find('i');
    const prevBtn = $('#playlist-prev-btn');
    const nextBtn = $('#playlist-next-btn');
    const progressBar = $('#playlist-progress-bar');
    const progress = $('#playlist-progress');
    const trackTitle = $('#playlist-track-title');

    progressBar.addClass('disabled');
    prevBtn.prop('disabled', true);
    nextBtn.prop('disabled', true);

    // --- 2. VYTVOŘENÍ AUDIO PRVKŮ (OPRAVENÁ LOGIKA) ---
    const headingMap = {
        '1. čtení': 'cteni_1',
        'evangelium': 'evangelium',
        '2. čtení': 'cteni_2'
    };
    
    // Projdeme všechny h3 nadpisy v obsahu
    $('.entry-content h3').each(function() {
        const headingElement = $(this);
        const headingText = headingElement.text().toLowerCase().trim();

        // Projdeme náš seznam klíčových slov
        for (const [keyword, audioKey] of Object.entries(headingMap)) {
            // Pokud nadpis obsahuje klíčové slovo a existuje pro něj audio
            if (headingText.includes(keyword) && audioUrls[audioKey]) {
                
                const audioElement = new Audio(audioUrls[audioKey]);
                audioElement.preload = 'none';
                audioElements[audioKey] = audioElement;
                
                const playerButton = $(`<button class="audio-player-button" data-audio-key="${audioKey}" aria-label="Přehrát ${keyword}"><i class="fa fa-headphones" aria-hidden="true"></i></button>`);
                
                // Přidáme tlačítko a třídy k nadpisu
                headingElement.wrapInner('<span class="heading-text"></span>').append(playerButton);
                headingElement.addClass('heading-with-player');
                
                // Nastavíme, co se stane po skončení přehrávání
                audioElement.onended = function() {
                    if (playlistState === 'playing' && playlist[currentTrackIndex] === audioKey) {
                        playTrackInPlaylist(currentTrackIndex + 1);
                    } else {
                        resetIndividualPlay();
                    }
                };

                // Aktualizace progress baru během přehrávání
                audioElement.ontimeupdate = function() {
                    if (playlistState !== 'stopped' && playlist[currentTrackIndex] === audioKey && this.duration) {
                        const progressPercent = (this.currentTime / this.duration) * 100;
                        progress.css('width', progressPercent + '%');
                    }
                };

                // Našli jsme shodu, můžeme přejít na další h3
                break; 
            }
        }
    });

    // Sestavení playlistu ve správném pořadí
    if (audioElements.cteni_1) playlist.push('cteni_1');
    if (audioElements.cteni_2) playlist.push('cteni_2');
    if (audioElements.evangelium) playlist.push('evangelium');
    
    // --- 3. FUNKCE PRO OVLÁDÁNÍ ---
    function updateNavButtonsState(index) {
        prevBtn.prop('disabled', index <= 0);
        nextBtn.prop('disabled', index >= playlist.length - 1);
    }

    function resetIndividualPlay() {
        if (currentlyPlaying) currentlyPlaying.element.pause();
        $('.audio-player-button').removeClass('is-playing is-paused').find('i').removeClass('fa-pause fa-play').addClass('fa-headphones');
        currentlyPlaying = null;
    }
    
    function resetPlaylist() {
        if (playlistState !== 'stopped' && currentTrackIndex > -1) {
            if (audioElements[playlist[currentTrackIndex]]) audioElements[playlist[currentTrackIndex]].pause();
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
        if (currentTrackIndex > -1 && currentTrackIndex < playlist.length) {
            const oldTrackKey = playlist[currentTrackIndex];
            if (audioElements[oldTrackKey]) {
                audioElements[oldTrackKey].pause();
            }
        }
        
        if (index >= playlist.length || index < 0) {
            resetPlaylist();
            resetIndividualPlay();
            return;
        }

        const trackKey = playlist[index];
        const audioElement = audioElements[trackKey];
        currentTrackIndex = index;
        playlistState = 'playing';
        
        resetIndividualPlay();
        
        $('.audio-player-button').removeClass('is-playing is-paused').find('i').removeClass('fa-pause fa-play').addClass('fa-headphones');
        $(`button[data-audio-key="${trackKey}"]`).addClass('is-playing').find('i').removeClass('fa-headphones fa-play').addClass('fa-pause');

        playPauseIcon.removeClass('fa-play').addClass('fa-pause');
        progressBar.removeClass('disabled');
        trackTitle.text(titleMap[trackKey] || '');
        updateNavButtonsState(index);

        audioElement.currentTime = 0;
        audioElement.play();
    }

    // --- 4. LISTENERS PRO OVLÁDACÍ PRVKY ---
    playPauseBtn.on('click', function() {
        resetIndividualPlay();
        if (playlistState === 'playing') {
            audioElements[playlist[currentTrackIndex]].pause();
            playlistState = 'paused';
            playPauseIcon.removeClass('fa-pause').addClass('fa-play');
            $(`button[data-audio-key="${playlist[currentTrackIndex]}"]`).removeClass('is-playing').addClass('is-paused').find('i').removeClass('fa-pause').addClass('fa-play');
        } else if (playlistState === 'paused') {
            audioElements[playlist[currentTrackIndex]].play();
            playlistState = 'playing';
            playPauseIcon.removeClass('fa-play').addClass('fa-pause');
            $(`button[data-audio-key="${playlist[currentTrackIndex]}"]`).removeClass('is-paused').addClass('is-playing').find('i').removeClass('fa-play').addClass('fa-pause');
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

    $('.audio-player-button').on('click', function(e) {
        e.stopPropagation();
        const clickedKey = $(this).data('audio-key');
        if (playlistState !== 'stopped') resetPlaylist();
        if (currentlyPlaying && currentlyPlaying.key === clickedKey) {
            currentlyPlaying.element.pause();
            $(this).removeClass('is-playing').addClass('is-paused').find('i').removeClass('fa-pause').addClass('fa-play');
            currentlyPlaying = null;
        } else {
            resetIndividualPlay();
            currentlyPlaying = { key: clickedKey, element: audioElements[clickedKey] };
            currentlyPlaying.element.currentTime = 0;
            currentlyPlaying.element.play();
            $(this).removeClass('is-paused').addClass('is-playing').find('i').removeClass('fa-headphones fa-play').addClass('fa-pause');
        }
    });
    
    progressBar.on('click', function(e) {
        if (playlistState === 'stopped') return;
        const barWidth = $(this).width();
        const clickX = e.offsetX;
        const audioElement = audioElements[playlist[currentTrackIndex]];
        if (audioElement.duration) {
            audioElement.currentTime = (clickX / barWidth) * audioElement.duration;
        }
    });

});