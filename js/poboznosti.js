/**
 * JavaScript pro stránku "Aplikace Pobožnosti".
 * VERZE 5: Finální oprava - přidána chybějící logika pro nahrazení [AUDIO] tagů za tlačítka.
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

    // --- 2. DYNAMICKÉ NAHRAZENÍ [AUDIO] TAGŮ ZA TLAČÍTKA ---
    $('.poboznosti-app .entry-content p, .poboznosti-app .entry-content h3').each(function() {
        const element = $(this);
        let element_html = element.html();
        const match = element_html.match(/\[AUDIO:([\w_]+)\]/);

        if (match) {
            const placeholder = match[0];
            const audioKey = match[1];

            if (audioUrls[audioKey]) {
                // TOTO JE KLÍČOVÁ ČÁST, KTERÁ CHYBĚLA:
                // 1. Odstraníme [AUDIO:...] z textu.
                element_html = element_html.replace(placeholder, '').trim();

                // 2. Vložíme upravený text zpět do elementu.
                element.html(element_html);
                
                // 3. Vytvoříme a přidáme tlačítko.
                const button = $(`<button class="poboznosti-audio-button" data-audio-key="${audioKey}" aria-label="Přehrát ${titleMap[audioKey] || audioKey}"><i class="fa fa-headphones"></i></button>`);
                element.append(button);
                
                // 4. Přidáme třídu pro stylování.
                element.addClass('has-audio');
                
                // ---------------------------------------------

                // Vytvoření audio prvků pro přehrávač (tato část byla správně)
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

    // Sestavení playlistu ve správném pořadí
    for (const key in audioUrls) {
        if (Object.prototype.hasOwnProperty.call(audioUrls, key)) {
            playlist.push(key);
        }
    }

    // --- 3. FUNKCE PRO OVLÁDÁNÍ (beze změn) ---
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

    // --- 4. LISTENERS PRO OVLÁDACÍ PRVKY (beze změn) ---
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
});