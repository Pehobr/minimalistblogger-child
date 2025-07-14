jQuery(document).ready(function($) {
    "use strict";

    const settingsContainer = $('#user-layout-settings');
    if (!settingsContainer.length) {
        return; 
    }

    $('.accordion-btn').on('click', function() {
        const content = $(this).next('.accordion-content');
        $('.accordion-content').not(content).slideUp(300);
        $('.accordion-btn').not(this).removeClass('active');
        content.slideToggle(300);
        $(this).toggleClass('active');
    });

    const visibilityStorageKey = 'pehobr_user_home_visibility';
    const displayStorageKey = 'pehobr_user_home_display';
    const themeStorageKey = 'pehobr_user_home_themes';
    const colorStorageKey = 'pehobr_user_light_theme_color';

    function loadSettings() {
        const savedVisibility = localStorage.getItem(visibilityStorageKey);
        const visibility = savedVisibility ? JSON.parse(savedVisibility) : {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('section-slug');
            const isVisible = (visibility[slug] === 'on' || typeof visibility[slug] === 'undefined');
            $(this).prop('checked', isVisible);
        });

        const savedDisplay = localStorage.getItem(displayStorageKey);
        const displaySettings = savedDisplay ? JSON.parse(savedDisplay) : {};
        settingsContainer.find('.display-toggle').each(function() {
            const slug = $(this).data('section-slug');
            const isTextMode = (displaySettings[slug] === 'textove');
            $(this).prop('checked', isTextMode);
        });

        const savedThemes = localStorage.getItem(themeStorageKey);
        const themeSettings = savedThemes ? JSON.parse(savedThemes) : {};
        settingsContainer.find('.theme-toggle').each(function() {
            const slug = $(this).data('section-slug');
            if (slug) {
                const isFialove = (themeSettings[slug] === 'fialove' || typeof themeSettings[slug] === 'undefined');
                $(this).prop('checked', isFialove);
            }
        });
        
        const savedColor = localStorage.getItem(colorStorageKey);
        const colorPicker = $('#light-theme-color-picker');
        if (colorPicker.length && savedColor) {
            colorPicker.val(savedColor);
        }
    }

    function saveSettings() {
        const visibility = {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('section-slug');
            visibility[slug] = $(this).is(':checked') ? 'on' : 'off';
        });
        localStorage.setItem(visibilityStorageKey, JSON.stringify(visibility));

        const displaySettings = {};
        settingsContainer.find('.display-toggle').each(function() {
            const slug = $(this).data('section-slug');
            displaySettings[slug] = $(this).is(':checked') ? 'textove' : 'graficke';
        });
        localStorage.setItem(displayStorageKey, JSON.stringify(displaySettings));
        
        const themeSettings = {};
        settingsContainer.find('.theme-toggle').each(function() {
            const slug = $(this).data('section-slug');
            if (slug) {
                themeSettings[slug] = $(this).is(':checked') ? 'fialove' : 'svetle';
            }
        });
        localStorage.setItem(themeStorageKey, JSON.stringify(themeSettings));

        const colorPicker = $('#light-theme-color-picker');
        if (colorPicker.length) {
            localStorage.setItem(colorStorageKey, colorPicker.val());
        }
    }

    // Při změně jakéhokoli přepínače zavoláme funkci pro uložení
    settingsContainer.on('change', '.visibility-toggle, .display-toggle, .theme-toggle', saveSettings);

    // Při změně barvy v hlavním color pickeru také uložíme
    settingsContainer.on('input', '#light-theme-color-picker', saveSettings);

    // OPRAVENO: Kliknutí na tlačítko s návrhem barvy
    settingsContainer.on('click', '.color-suggestion-btn', function() {
        const newColor = $(this).data('color');
        const colorPicker = $('#light-theme-color-picker');
        
        // 1. Nastavíme hodnotu do hlavního color pickeru
        colorPicker.val(newColor);
        
        // 2. Přímo zavoláme funkci pro uložení nastavení, což je spolehlivější
        saveSettings(); 
    });

    loadSettings();
});