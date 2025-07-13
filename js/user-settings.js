jQuery(document).ready(function($) {
    "use strict";

    const settingsContainer = $('#user-layout-settings');
    if (!settingsContainer.length) {
        return; // Skript se nespustí, pokud na stránce není kontejner s nastavením
    }

    // --- LOGIKA PRO AKORDEON ---
    $('.accordion-btn').on('click', function() {
        const content = $(this).next('.accordion-content');
        content.slideToggle(300);
        $('.accordion-content').not(content).slideUp(300);
    });

    const visibilityStorageKey = 'pehobr_user_home_visibility';
    const displayStorageKey = 'pehobr_user_home_display';
    // --- NOVÝ KÓD START ---
    const themeStorageKey = 'pehobr_user_global_theme';
    // --- NOVÝ KÓD KONEC ---

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
            const isTextMode = (displaySettings[slug] === 'textove');
            $(this).prop('checked', isTextMode);
        });

        // --- NOVÝ KÓD START ---
        // Načtení globálního motivu
        const savedTheme = localStorage.getItem(themeStorageKey);
        // 'fialove' je true (zaškrtnuto), 'svetle' je false (odškrtnuto)
        const isFialove = (savedTheme === 'fialove');
        settingsContainer.find('.theme-toggle').prop('checked', isFialove);
        // --- NOVÝ KÓD KONEC ---
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
        
        // --- NOVÝ KÓD START ---
        // Uložení globálního motivu
        const theme = settingsContainer.find('.theme-toggle').is(':checked') ? 'fialove' : 'svetle';
        localStorage.setItem(themeStorageKey, theme);
        // --- NOVÝ KÓD KONEC ---
    }

    // --- Event Listeners ---

    // Při změně jakéhokoli přepínače uložíme nové nastavení
    // --- UPRAVENÝ KÓD START ---
    settingsContainer.on('change', '.visibility-toggle, .display-toggle, .theme-toggle', function() {
        saveSettings();
    });
    // --- UPRAVENÝ KÓD KONEC ---

    // Při načtení stránky načteme a aplikujeme uložená nastavení
    loadSettings();
});