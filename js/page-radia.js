jQuery(document).ready(function($) {
    const playerContainer = $('#radio-player-container');
    if (!playerContainer.length) return;

    // =================================================================
    // ČÁST 1: LOGIKA PRO PŘEHRÁVÁNÍ RÁDIÍ (zůstává podobná)
    // =================================================================
    const audioPlayer = new Audio();
    audioPlayer.preload = 'none';
    let currentlyPlayingButton = null;

    function resetAllButtons() {
        $('.radio-play-btn').removeClass('is-playing').find('i').removeClass('fa-pause fa-spinner fa-spin').addClass('fa-play');
        $('.radio-player-item').removeClass('is-playing');
    }

    // Event listener je navázán na kontejner, aby fungoval i pro dynamicky přidaná rádia
    playerContainer.on('click', '.radio-play-btn', function() {
        const clickedButton = $(this);
        const icon = clickedButton.find('i');
        const streamUrl = clickedButton.closest('.radio-player-item').data('stream-url');

        if (currentlyPlayingButton && currentlyPlayingButton[0] === clickedButton[0]) {
            audioPlayer.pause();
            resetAllButtons();
            currentlyPlayingButton = null;
            return;
        }

        resetAllButtons();
        icon.removeClass('fa-play').addClass('fa-spinner fa-spin');

        audioPlayer.src = streamUrl;
        currentlyPlayingButton = clickedButton;

        const playPromise = audioPlayer.play();

        if (playPromise !== undefined) {
            playPromise.then(_ => {
                icon.removeClass('fa-spinner fa-spin').addClass('fa-pause');
                clickedButton.addClass('is-playing');
                clickedButton.closest('.radio-player-item').addClass('is-playing');
            }).catch(error => {
                console.error("Chyba při spouštění rádia:", error);
                alert("Rádio se nepodařilo spustit. Stream může být dočasně nedostupný.");
                resetAllButtons();
                currentlyPlayingButton = null;
            });
        }
    });

    $(window).on('beforeunload', function() {
        if (audioPlayer) {
            audioPlayer.pause();
        }
    });

    // =================================================================
    // ČÁST 2: LOGIKA PRO PŘIDÁVÁNÍ VLASTNÍCH RÁDIÍ
    // =================================================================
    const showFormBtn = $('#show-add-radio-form-btn');
    const hideFormBtn = $('#hide-add-radio-form-btn');
    const formContainer = $('#add-radio-form-container');
    const customRadioForm = $('#custom-radio-form');
    const radioNameInput = $('#custom-radio-name');
    const radioStreamInput = $('#custom-radio-stream');
    
    const storageKey = 'pehobr_custom_radios';
    // <<<=== ZMĚNA ZDE ===>>>
    const defaultIconUrl = radio_page_settings.template_url + '/img/ikona-vlastni.png';

    // Zobrazit formulář
    showFormBtn.on('click', function() {
        formContainer.slideDown();
        $(this).parent().hide();
    });

    // Skrýt formulář
    hideFormBtn.on('click', function() {
        formContainer.slideUp();
        showFormBtn.parent().show();
    });

    // Uložení rádia
    customRadioForm.on('submit', function(event) {
        event.preventDefault();
        
        const name = radioNameInput.val().trim();
        const stream = radioStreamInput.val().trim();

        if (!name || !stream) {
            alert('Prosím, vyplňte název i URL adresu streamu.');
            return;
        }

        const newRadio = { name: name, stream: stream };

        // Načteme stávající rádia, přidáme nové a uložíme
        const radios = getCustomRadios();
        radios.push(newRadio);
        saveCustomRadios(radios);

        // Zobrazíme nové rádio na stránce
        renderSingleRadio(newRadio);
        
        // Vyčistíme a skryjeme formulář
        radioNameInput.val('');
        radioStreamInput.val('');
        formContainer.slideUp();
        showFormBtn.parent().show();
    });

    // Funkce pro načtení rádií z localStorage
    function getCustomRadios() {
        const radiosJSON = localStorage.getItem(storageKey);
        return radiosJSON ? JSON.parse(radiosJSON) : [];
    }

    // Funkce pro uložení pole rádií do localStorage
    function saveCustomRadios(radios) {
        localStorage.setItem(storageKey, JSON.stringify(radios));
    }

    // Funkce pro vykreslení jednoho rádia na stránce
    function renderSingleRadio(radio) {
        // Bezpečnostní ošetření textu
        const safeName = $('<div>').text(radio.name).html();
        const safeStream = $('<div>').text(radio.stream).html();

        const radioHtml = `
            <div class="radio-player-item" data-stream-url="${safeStream}">
                <img src="${defaultIconUrl}" alt="${safeName}" class="radio-icon">
                <h2 class="radio-title">${safeName}</h2>
                <button class="radio-play-btn" aria-label="Přehrát ${safeName}">
                    <i class="fa fa-play" aria-hidden="true"></i>
                </button>
            </div>`;
        
        playerContainer.append(radioHtml);
    }
    
    // Načteme a zobrazíme všechna uživatelská rádia při startu stránky
    const allCustomRadios = getCustomRadios();
    allCustomRadios.forEach(function(radio) {
        renderSingleRadio(radio);
    });
});