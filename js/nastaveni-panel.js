/**
 * JavaScript pro panel nastavení.
 * Bude načítán na stránkách Aplikace Liturgické Čtení a Aplikace Pobožnosti.
 */
jQuery(document).ready(function($) {

    // 1. Vytvoření ikony a její vložení do obsahu příspěvku
    const settingsIconHTML = '<button id="settings-open-btn" aria-label="Otevřít nastavení"><i class="fa fa-bars"></i></button>';
    // Hledáme první centrovaný odstavec v obsahu článku, který je v aplikaci.
    // Specifičtější selektor pro zabránění vložení na jiná místa.
    const targetParagraph = $('.liturgicke-cteni-app .entry-content p[style*="text-align: center"]').first();
    const poboznostiTargetParagraph = $('.poboznosti-app .entry-content p[style*="text-align: center"]').first();

    if (targetParagraph.length) {
        targetParagraph.append(settingsIconHTML);
    } else if (poboznostiTargetParagraph.length) {
        poboznostiTargetParagraph.append(settingsIconHTML);
    }


    // 2. Selekce všech potřebných prvků
    const settingsPanel = $('#settings-panel');
    const settingsOverlay = $('#settings-overlay');
    const openBtn = $('#settings-open-btn'); // Nyní se selektor odkazuje na nově vytvořené tlačítko
    const closeBtn = $('#settings-close-btn');
    const playerToggle = $('#toggle-player');
    
    // Zde potřebujeme rozlišit hlavní přehrávač pro Liturgické čtení a Pobožnosti
    // Jelikož oba mají různé ID (#playlist-player-container a #poboznosti-player-container),
    // potřebujeme dynamicky vybrat ten, který je na aktuální stránce.
    const mainPlayerContainer = $('#playlist-player-container').length ? $('#playlist-player-container') : $('#poboznosti-player-container');

    const storageKey = 'app_showPlayer'; // Univerzální klíč pro lokální úložiště

    // 3. Funkce pro otevírání a zavírání panelu
    function openSettings() {
        settingsPanel.addClass('is-open');
        settingsOverlay.addClass('is-visible');
    }

    function closeSettings() {
        settingsPanel.removeClass('is-open');
        settingsOverlay.removeClass('is-visible');
    }

    // 4. Aplikace uloženého nastavení při načtení stránky
    function applySavedSettings() {
        if (mainPlayerContainer.length) { // Kontrolujeme, zda kontejner existuje na stránce
            const shouldShowPlayer = localStorage.getItem(storageKey);
            if (shouldShowPlayer === 'false') {
                mainPlayerContainer.hide();
                playerToggle.prop('checked', false);
            } else { // Pokud není uloženo nebo je 'true'
                mainPlayerContainer.show();
                playerToggle.prop('checked', true);
            }
        } else {
            // Pokud na stránce není žádný přehrávač, skryjeme přepínač v panelu nastavení
            playerToggle.closest('.setting-item').hide();
        }
    }

    // 5. Přidání posluchačů událostí (Event Listeners)
    // Musíme použít delegované události, protože tlačítko '#settings-open-btn' je dynamicky vkládáno
    $(document).on('click', '#settings-open-btn', openSettings);
    
    closeBtn.on('click', closeSettings);
    settingsOverlay.on('click', closeSettings);

    playerToggle.on('change', function() {
        if (mainPlayerContainer.length) { // Znovu kontrolujeme existenci přehrávače
            if ($(this).is(':checked')) {
                mainPlayerContainer.slideDown();
                localStorage.setItem(storageKey, 'true');
            } else {
                mainPlayerContainer.slideUp();
                localStorage.setItem(storageKey, 'false');
            }
        }
    });

    // 6. Spuštění funkce po načtení stránky
    applySavedSettings();
});