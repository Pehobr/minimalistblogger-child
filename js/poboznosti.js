/**
 * JavaScript pro stránku "Aplikace Pobožnosti".
 * VERZE 2: Tlačítko vpravo, hledání i v H3.
 */
jQuery(document).ready(function($) {

    if (typeof poboznostiUdaje === 'undefined' || typeof poboznostiUdaje.audioUrls === 'undefined' || Object.keys(poboznostiUdaje.audioUrls).length === 0) {
        if ($('#poboznosti-player-container').length) $('#poboznosti-player-container').hide();
        return;
    }

    const audioUrls = poboznostiUdaje.audioUrls;
    const audioElements = {};
    let currentlyPlaying = null;
    const playlist = [];
    let currentTrackIndex = -1;
    let playlistState = 'stopped';

    const titleMap = {};
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

    // --- Dynamické vkládání tlačítek a sestavení playlistu ---
    // ZMĚNA: Hledáme v odstavcích <p> i v nadpisech <h3>
    $('.poboznosti-app .entry-content p, .poboznosti-app .entry-content h3').each(function() {
        const element = $(this);
        let element_html = element.html();
        const match = element_html.match(/\[AUDIO:(audio_cast_\d+)\]/);

        if (match) {
            const placeholder = match[0];
            const audioKey = match[1];

            if (audioUrls[audioKey]) {
                // Remove the placeholder from the HTML content
                element_html = element_html.replace(placeholder, '').trim();

                // Wrap the text content in a span to properly apply flex layout
                // a pak přidejte tlačítko vedle něj.
                element.html(`<span class="audio-text-content">${element_html}</span>`);
                element.addClass('has-audio');

                const button = $(`<button class="poboznosti-audio-button" data-audio-key="${audioKey}" aria-label="Přehrát ${audioKey}"><i class="fa fa-headphones"></i></button>`);

                // ZMĚNA: Vkládáme tlačítko na konec (vpravo) místo na začátek
                element.append(button);

                const audioElement = new Audio(audioUrls[audioKey]);
                audioElement.preload = 'none';
                audioElements[audioKey] = audioElement;
                playlist.push(audioKey);

                const trackNum = audioKey.split('_')[2];
                titleMap[audioKey] = `Část ${trackNum}`;

                audioElement.onended = function() {
                    if (playlistState === 'playing' && playlist[currentTrackIndex] === audioKey) {
                        playTrackInPlaylist(currentTrackIndex + 1);
                    } else {
                        resetIndividualPlay();
                    }
                };
                audioElement.ontimeupdate = function() {
                    if (playlistState !== 'stopped' && playlist[currentTrackIndex] === audioKey && this.duration) {
                        progress.css('width', ((this.currentTime / this.duration) * 100) + '%');
                    }
                };
            }
        }
    });

    // --- Funkce pro ovládání ---
    function updateNavButtonsState(index) { prevBtn.prop('disabled', index <= 0); nextBtn.prop('disabled', index >= playlist.length - 1); }
    function resetIndividualPlay() { if (currentlyPlaying) currentlyPlaying.element.pause(); $('.poboznosti-audio-button').removeClass('is-playing is-paused').find('i').removeClass('fa-pause fa-play').addClass('fa-headphones'); currentlyPlaying = null; }
    function resetPlaylist() { if (playlistState !== 'stopped' && currentTrackIndex > -1) { if (audioElements[playlist[currentTrackIndex]]) audioElements[playlist[currentTrackIndex]].pause(); } playlistState = 'stopped'; currentTrackIndex = -1; playPauseIcon.removeClass('fa-pause').addClass('fa-play'); progress.css('width', '0%'); progressBar.addClass('disabled'); trackTitle.text(''); updateNavButtonsState(-1); }
    function playTrackInPlaylist(index) { if (currentTrackIndex > -1) { audioElements[playlist[currentTrackIndex]]?.pause(); } if (index >= playlist.length || index < 0) { resetPlaylist(); resetIndividualPlay(); return; } const trackKey = playlist[index]; const audioElement = audioElements[trackKey]; currentTrackIndex = index; playlistState = 'playing'; resetIndividualPlay(); $('.poboznosti-audio-button').removeClass('is-playing is-paused').find('i').removeClass('fa-pause fa-play').addClass('fa-headphones'); $(`button[data-audio-key="${trackKey}"]`).addClass('is-playing').find('i').removeClass('fa-headphones').addClass('fa-pause'); playPauseIcon.removeClass('fa-play').addClass('fa-pause'); progressBar.removeClass('disabled'); trackTitle.text(titleMap[trackKey] || ''); updateNavButtonsState(index); audioElement.currentTime = 0; audioElement.play(); }

    // --- Listeners pro ovládací prvky ---
    playPauseBtn.on('click', function() { resetIndividualPlay(); if (playlistState === 'playing') { audioElements[playlist[currentTrackIndex]].pause(); playlistState = 'paused'; playPauseIcon.removeClass('fa-pause').addClass('fa-play'); $(`button[data-audio-key="${playlist[currentTrackIndex]}"]`).removeClass('is-playing').addClass('is-paused').find('i').removeClass('fa-pause').addClass('fa-play'); } else if (playlistState === 'paused') { audioElements[playlist[currentTrackIndex]].play(); playlistState = 'playing'; playPauseIcon.removeClass('fa-play').addClass('fa-pause'); $(`button[data-audio-key="${playlist[currentTrackIndex]}"]`).removeClass('is-paused').addClass('is-playing').find('i').removeClass('fa-play').addClass('fa-pause'); } else { playTrackInPlaylist(0); } });
    prevBtn.on('click', function() { if (playlistState !== 'stopped') playTrackInPlaylist(currentTrackIndex - 1); });
    nextBtn.on('click', function() { if (playlistState !== 'stopped') playTrackInPlaylist(currentTrackIndex + 1); });
    $(document).on('click', '.poboznosti-audio-button', function(e) { e.stopPropagation(); const clickedKey = $(this).data('audio-key'); if (playlistState !== 'stopped') resetPlaylist(); if (currentlyPlaying && currentlyPlaying.key === clickedKey) { currentlyPlaying.element.pause(); $(this).removeClass('is-playing').addClass('is-paused').find('i').removeClass('fa-pause').addClass('fa-play'); currentlyPlaying = null; } else { resetIndividualPlay(); currentlyPlaying = { key: clickedKey, element: audioElements[clickedKey] }; currentlyPlaying.element.currentTime = 0; currentlyPlaying.element.play(); $(this).removeClass('is-paused').addClass('is-playing').find('i').removeClass('fa-headphones').addClass('fa-pause'); } });
    progressBar.on('click', function(e) { if (playlistState === 'stopped') return; const barWidth = $(this).width(); const clickX = e.offsetX; const audioElement = audioElements[playlist[currentTrackIndex]]; if (audioElement.duration) { audioElement.currentTime = (clickX / barWidth) * audioElement.duration; } });
});