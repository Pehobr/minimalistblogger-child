jQuery(document).ready(function($) {
    const playerContainer = $('#radio-player-container');
    if (!playerContainer.length) return;

    // =================================================================
    // ČÁST 1: LOGIKA PRO PŘEHRÁVÁNÍ RÁDIÍ
    // =================================================================
    const audioPlayer = new Audio();
    audioPlayer.preload = 'none';
    let currentlyPlayingButton = null;

    function resetAllButtons() {
        $('.radio-play-btn').removeClass('is-playing').find('i').removeClass('fa-pause fa-spinner fa-spin').addClass('fa-play');
        $('.radio-player-item').removeClass('is-playing');
    }

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
        if (audioPlayer) audioPlayer.pause();
    });

    // =================================================================
    // ČÁST 2: SPRÁVA VLASTNÍCH RÁDIÍ (PŘIDÁNÍ, MAZÁNÍ, ÚPRAVY)
    // =================================================================
    const showFormBtn = $('#show-add-radio-form-btn');
    const hideFormBtn = $('#hide-add-radio-form-btn');
    const formContainer = $('#add-radio-form-container');
    const customRadioForm = $('#custom-radio-form');
    const radioNameInput = $('#custom-radio-name');
    const radioStreamInput = $('#custom-radio-stream');
    
    const storageKey = 'pehobr_custom_radios';
    const defaultIconUrl = radio_page_settings.template_url + '/img/ikona-vlastni.png';

    // Pomocná funkce pro bezpečné vložení textu do HTML
    function escapeHTML(str) {
        return $('<div>').text(str).html();
    }

    // --- Zobrazování a skrývání formuláře pro přidání ---
    showFormBtn.on('click', function() {
        formContainer.slideDown();
        $(this).parent().hide();
    });
    hideFormBtn.on('click', function() {
        formContainer.slideUp();
        showFormBtn.parent().show();
    });

    // --- Přidání nového rádia ---
    customRadioForm.on('submit', function(event) {
        event.preventDefault();
        const name = radioNameInput.val().trim();
        const stream = radioStreamInput.val().trim();
        if (!name || !stream) {
            alert('Prosím, vyplňte název i URL adresu streamu.');
            return;
        }
        const newRadio = { id: Date.now().toString(), name: name, stream: stream };
        const radios = getCustomRadios();
        radios.push(newRadio);
        saveCustomRadios(radios);
        renderSingleRadio(newRadio);
        radioNameInput.val('');
        radioStreamInput.val('');
        formContainer.slideUp();
        showFormBtn.parent().show();
    });

    // --- Mazání rádia ---
    playerContainer.on('click', '.delete-radio-btn', function() {
        if (!confirm('Opravdu chcete smazat toto rádio?')) return;
        
        const radioItem = $(this).closest('.custom-radio');
        const radioId = radioItem.data('id');
        
        const radios = getCustomRadios();
        const updatedRadios = radios.filter(radio => radio.id != radioId); // Používáme != pro jistotu
        saveCustomRadios(updatedRadios);
        
        radioItem.fadeOut(300, function() { $(this).remove(); });
    });

    // --- Úprava rádia (krok 1: zobrazení editačního formuláře) ---
    playerContainer.on('click', '.edit-radio-btn', function() {
        const radioItem = $(this).closest('.custom-radio');
        const radioId = radioItem.data('id');
        const radios = getCustomRadios();
        const radioToEdit = radios.find(radio => radio.id == radioId); // Používáme == pro jistotu

        if (!radioToEdit) {
            console.error("Chyba: Rádio k úpravě nebylo nalezeno. ID:", radioId);
            return;
        }

        const editFormHTML = `
            <div class="inline-edit-form">
                <h3>Upravit rádio</h3>
                <div class="form-group">
                    <label>Název rádia:</label>
                    <input type="text" class="edit-name-input" value="${escapeHTML(radioToEdit.name)}">
                </div>
                <div class="form-group">
                    <label>URL adresa streamu:</label>
                    <input type="url" class="edit-stream-input" value="${escapeHTML(radioToEdit.stream)}">
                </div>
                <div class="form-actions">
                    <button type="button" class="save-btn save-edit-btn">Uložit změny</button>
                    <button type="button" class="cancel-btn cancel-edit-btn">Zrušit</button>
                </div>
            </div>`;
        
        radioItem.find('.radio-item-main, .radio-item-actions').hide();
        radioItem.append(editFormHTML);
    });
    
    // --- Úprava rádia (krok 2: uložení změn) ---
    playerContainer.on('click', '.save-edit-btn', function() {
        const radioItem = $(this).closest('.custom-radio');
        const radioId = radioItem.data('id');
        
        const newName = radioItem.find('.edit-name-input').val().trim();
        const newStream = radioItem.find('.edit-stream-input').val().trim();

        if (!newName || !newStream) {
            alert('Název ani URL adresa nesmí být prázdné.');
            return;
        }

        let radios = getCustomRadios();
        const radioIndex = radios.findIndex(radio => radio.id == radioId);
        
        if (radioIndex > -1) {
            radios[radioIndex].name = newName;
            radios[radioIndex].stream = newStream;
            saveCustomRadios(radios);
            
            const newRadioItemHTML = generateRadioHTML(radios[radioIndex]);
            radioItem.replaceWith(newRadioItemHTML);
        }
    });

    // --- Úprava rádia (krok 3: zrušení úprav) ---
    playerContainer.on('click', '.cancel-edit-btn', function() {
        const radioItem = $(this).closest('.custom-radio');
        radioItem.find('.inline-edit-form').remove();
        radioItem.find('.radio-item-main, .radio-item-actions').show();
    });

    // --- Funkce pro práci s LocalStorage a vykreslování ---
    function getCustomRadios() {
        const radiosJSON = localStorage.getItem(storageKey);
        let radios = radiosJSON ? JSON.parse(radiosJSON) : [];
        let needsSave = false;

        // OPRAVA: Zkontrolujeme a případně doplníme chybějící ID
        radios.forEach((radio, index) => {
            if (!radio.hasOwnProperty('id') || !radio.id) {
                radio.id = (Date.now() + index).toString(); // Přidělíme unikátní ID
                needsSave = true;
            }
        });

        // Pokud jsme nějaká ID doplnili, uložíme opravená data zpět
        if (needsSave) {
            saveCustomRadios(radios);
        }

        return radios;
    }

    function saveCustomRadios(radios) {
        localStorage.setItem(storageKey, JSON.stringify(radios));
    }

    function generateRadioHTML(radio) {
        const safeName = escapeHTML(radio.name);
        const safeStream = escapeHTML(radio.stream);

        return `
            <div class="radio-player-item custom-radio" data-stream-url="${safeStream}" data-id="${radio.id}">
                <div class="radio-item-main">
                    <img src="${defaultIconUrl}" alt="${safeName}" class="radio-icon">
                    <div class="radio-title-container">
                        <h2 class="radio-title">${safeName}</h2>
                    </div>
                    <button class="radio-play-btn" aria-label="Přehrát ${safeName}">
                        <i class="fa fa-play" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="radio-item-actions">
                    <button class="edit-radio-btn" aria-label="Upravit rádio"><i class="fa fa-pencil"></i></button>
                    <button class="delete-radio-btn" aria-label="Smazat rádio"><i class="fa fa-trash"></i></button>
                </div>
            </div>`;
    }

    function renderSingleRadio(radio) {
        playerContainer.append(generateRadioHTML(radio));
    }
    
    // Načteme a zobrazíme všechna uložená rádia při startu stránky
    const allCustomRadios = getCustomRadios();
    allCustomRadios.forEach(radio => renderSingleRadio(radio));
});