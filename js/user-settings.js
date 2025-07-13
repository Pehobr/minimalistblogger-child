jQuery(document).ready(function($) {
    "use strict";

    const settingsContainer = $('#user-layout-settings');
    if (!settingsContainer.length) {
        return; // Skript se nespustí, pokud na stránce není kontejner s nastavením
    }

    // === NOVÁ LOGIKA PRO AKORDEON ===
    $('.accordion-btn').on('click', function() {
        // Najde následující prvek, což je .accordion-content
        const content = $(this).next('.accordion-content');
        
        // Plynule zobrazí nebo skryje obsah
        content.slideToggle(300);

        // Skryje ostatní otevřené obsahy, aby byl vždy otevřený jen jeden
        $('.accordion-content').not(content).slideUp(300);
    });
    // === KONEC LOGIKY PRO AKORDEON ===


    const visibilityStorageKey = 'pehobr_user_home_visibility';
    const displayStorageKey = 'pehobr_user_home_display';

    // --- Funkce pro načtení a uložení nastavení ---

    function loadSettings() {
        // Načtení viditelnosti sekcí
        const savedVisibility = localStorage.getItem(visibilityStorageKey);
        const visibility = savedVisibility ? JSON.parse(savedVisibility) : {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('section-slug');
            const isVisible = (visibility[slug] === 'on' || typeof visibility[slug] === 'undefined');
            $(this).prop('checked', isVisible);
        });

        // Načtení zobrazení (display) pro specifické sekce
        const savedDisplay = localStorage.getItem(displayStorageKey);
        const displaySettings = savedDisplay ? JSON.parse(savedDisplay) : {};
        settingsContainer.find('.display-toggle').each(function() {
            const slug = $(this).data('section-slug');
            // 'textove' je true (zaškrtnuto), 'graficke' je false (odškrtnuto)
            const isTextMode = (displaySettings[slug] === 'textove');
            $(this).prop('checked', isTextMode);
        });
    }

    function saveSettings() {
        // Uložení viditelnosti
        const visibility = {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('section-slug');
            visibility[slug] = $(this).is(':checked') ? 'on' : 'off';
        });
        localStorage.setItem(visibilityStorageKey, JSON.stringify(visibility));

        // Uložení zobrazení
        const displaySettings = {};
        settingsContainer.find('.display-toggle').each(function() {
            const slug = $(this).data('section-slug');
            displaySettings[slug] = $(this).is(':checked') ? 'textove' : 'graficke';
        });
        localStorage.setItem(displayStorageKey, JSON.stringify(displaySettings));
    }

    // --- Event Listeners ---

    // Při změně jakéhokoli přepínače uložíme nové nastavení
    settingsContainer.on('change', '.visibility-toggle, .display-toggle', function() {
        saveSettings();
    });

    // Při načtení stránky načteme a aplikujeme uložená nastavení
    loadSettings();
});