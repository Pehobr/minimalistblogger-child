jQuery(document).ready(function($) {
    "use strict";

    const settingsContainer = $('#user-layout-settings');
    if (!settingsContainer.length) {
        return; 
    }

    // Akordeon pro rozbalování sekcí
    $('.accordion-btn').on('click', function() {
        const content = $(this).next('.accordion-content');
        
        // Zavřít ostatní, otevřít aktuální
        $('.accordion-content').not(content).slideUp(300);
        $('.accordion-btn').not(this).removeClass('active');
        
        content.slideToggle(300);
        $(this).toggleClass('active');
    });

    // Klíče pro ukládání dat do localStorage
    const visibilityStorageKey = 'pehobr_user_home_visibility';
    const displayStorageKey = 'pehobr_user_home_display';
    const themeStorageKey = 'pehobr_user_home_themes';
    const colorStorageKey = 'pehobr_user_light_theme_color'; // <-- NOVÝ KLÍČ pro barvu

    /**
     * Načte všechna uložená nastavení z localStorage a aplikuje je na stránce.
     */
    function loadSettings() {
        // Načtení viditelnosti sekcí
        const savedVisibility = localStorage.getItem(visibilityStorageKey);
        const visibility = savedVisibility ? JSON.parse(savedVisibility) : {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('section-slug');
            const isVisible = (visibility[slug] === 'on' || typeof visibility[slug] === 'undefined');
            $(this).prop('checked', isVisible);
        });

        // Načtení zobrazení (Grafika/Text)
        const savedDisplay = localStorage.getItem(displayStorageKey);
        const displaySettings = savedDisplay ? JSON.parse(savedDisplay) : {};
        settingsContainer.find('.display-toggle').each(function() {
            const slug = $(this).data('section-slug');
            const isTextMode = (displaySettings[slug] === 'textove');
            $(this).prop('checked', isTextMode);
        });

        // Načtení barevného tématu (Světlé/Fialové)
        const savedThemes = localStorage.getItem(themeStorageKey);
        const themeSettings = savedThemes ? JSON.parse(savedThemes) : {};
        settingsContainer.find('.theme-toggle').each(function() {
            const slug = $(this).data('section-slug');
            if (slug) {
                const isFialove = (themeSettings[slug] === 'fialove' || typeof themeSettings[slug] === 'undefined');
                $(this).prop('checked', isFialove);
            }
        });
        
        // --- NOVÁ ČÁST ---
        // Načtení vlastní barvy světlého pozadí
        const savedColor = localStorage.getItem(colorStorageKey);
        const colorPicker = $('#light-theme-color-picker');
        if (colorPicker.length && savedColor) {
            colorPicker.val(savedColor);
        }
        // --- KONEC NOVÉ ČÁSTI ---
    }

    /**
     * Uloží aktuální stav všech nastavení do localStorage.
     */
    function saveSettings() {
        // Uložení viditelnosti
        const visibility = {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('section-slug');
            visibility[slug] = $(this).is(':checked') ? 'on' : 'off';
        });
        localStorage.setItem(visibilityStorageKey, JSON.stringify(visibility));

        // Uložení zobrazení (Grafika/Text)
        const displaySettings = {};
        settingsContainer.find('.display-toggle').each(function() {
            const slug = $(this).data('section-slug');
            displaySettings[slug] = $(this).is(':checked') ? 'textove' : 'graficke';
        });
        localStorage.setItem(displayStorageKey, JSON.stringify(displaySettings));
        
        // Uložení barevného tématu (Světlé/Fialové)
        const themeSettings = {};
        settingsContainer.find('.theme-toggle').each(function() {
            const slug = $(this).data('section-slug');
            if (slug) {
                themeSettings[slug] = $(this).is(':checked') ? 'fialove' : 'svetle';
            }
        });
        localStorage.setItem(themeStorageKey, JSON.stringify(themeSettings));

        // --- NOVÁ ČÁST ---
        // Uložení vlastní barvy světlého pozadí
        const colorPicker = $('#light-theme-color-picker');
        if (colorPicker.length) {
            localStorage.setItem(colorStorageKey, colorPicker.val());
        }
        // --- KONEC NOVÉ ČÁSTI ---
    }

    // Při změně jakéhokoli přepínače zavoláme funkci pro uložení
    settingsContainer.on('change', '.visibility-toggle, .display-toggle, .theme-toggle', saveSettings);

    // Při změně barvy v color pickeru také uložíme (používáme 'input' pro okamžitou reakci)
    settingsContainer.on('input', '#light-theme-color-picker', saveSettings);

    // Načteme nastavení ihned po načtení stránky
    loadSettings();
});