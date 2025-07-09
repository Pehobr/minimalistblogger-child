/**
 * JavaScript pro panel nastavení.
 * VERZE 3: Robustnější vkládání ikony do hlavičky příspěvku.
 */
jQuery(document).ready(function($) {

    // 1. Vytvoření ikony a její vložení
    const settingsIconHTML = '<button id="settings-open-btn" aria-label="Otevřít nastavení"><i class="fa fa-cog"></i></button>';
    
    // Cílíme na hlavičku článku uvnitř kontejneru aplikace, což je stabilní prvek
    const targetHeader = $('.poboznosti-app .posts-entry > .entry-header, .liturgicke-cteni-app .posts-entry > .entry-header').first();

    if (targetHeader.length) {
        targetHeader.css('position', 'relative'); // Důležité pro pozicování ikony
        targetHeader.append(settingsIconHTML);
    }

    // 2. Selekce všech potřebných prvků
    const settingsPanel = $('#settings-panel');
    const settingsOverlay = $('#settings-overlay');
    const openBtn = $('#settings-open-btn');
    const closeBtn = $('#settings-close-btn');
    const playerToggle = $('#toggle-player');
    
    const mainPlayerContainer = $('#playlist-player-container').length ? $('#playlist-player-container') : $('#poboznosti-player-container');
    const storageKey = 'app_showPlayer';

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
        if (mainPlayerContainer.length) {
            const shouldShowPlayer = localStorage.getItem(storageKey);
            if (shouldShowPlayer === 'false') {
                mainPlayerContainer.hide();
                playerToggle.prop('checked', false);
            } else {
                mainPlayerContainer.show();
                playerToggle.prop('checked', true);
            }
        } else {
            playerToggle.closest('.setting-item').hide();
        }
    }

    // 5. Přidání posluchačů událostí
    $(document).on('click', '#settings-open-btn', openSettings);
    closeBtn.on('click', closeSettings);
    settingsOverlay.on('click', closeSettings);

    playerToggle.on('change', function() {
        if (mainPlayerContainer.length) {
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