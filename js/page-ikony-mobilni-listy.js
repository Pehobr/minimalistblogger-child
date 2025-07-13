jQuery(document).ready(function($) {
    "use strict";

    // Kontrolní výpis pro ověření správné verze souboru
    console.log('Načten skript pro nastavení ikon v.1.1');

    const settingsContainer = $('#mobile-nav-settings');
    if (!settingsContainer.length) {
        return;
    }

    // --- LOGIKA PRO AKORDEON ---
    $('.accordion-btn').on('click', function() {
        const content = $(this).next('.accordion-content');
        content.slideToggle(300);
        $(this).toggleClass('active');
        $('.accordion-content').not(content).slideUp(300);
        $('.accordion-btn').not(this).removeClass('active');
    });

    // --- LOGIKA PRO NASTAVENÍ IKON ---
    const navSettingsKey = 'pehobr_mobile_nav_settings';

    // Funkce pro načtení uložených nastavení a jejich aplikace
    function loadAndApplyNavSettings() {
        const savedSettings = JSON.parse(localStorage.getItem(navSettingsKey)) || {};

        // Aplikace na stránce s nastavením
        for (const [position, settings] of Object.entries(savedSettings)) {
            settingsContainer.find(`input[name="nav-icon-${position}"][value="${settings.value}"]`).prop('checked', true);
        }

        // Aplikace na samotnou navigační lištu
        const navBar = $('.bottom-nav-bar');
        if (navBar.length) {
             for (const [position, settings] of Object.entries(savedSettings)) {
                const linkElement = navBar.find(`li:nth-child(${position}) a`);
                const iconElement = linkElement.find('i');

                if(linkElement.length && iconElement.length) {
                    linkElement.attr('href', settings.url);
                    iconElement.removeClass().addClass('fa ' + settings.icon);
                }
            }
        }
    }

    // Funkce pro uložení nastavení
    function saveNavSettings() {
        const currentSettings = JSON.parse(localStorage.getItem(navSettingsKey)) || {};

        settingsContainer.find('.setting-item').each(function() {
            const position = $(this).data('nav-position');
            const checkedRadio = $(this).find('input[type="radio"]:checked');

            if (checkedRadio.length) {
                currentSettings[position] = {
                    value: checkedRadio.val(),
                    icon: checkedRadio.data('icon'),
                    url: checkedRadio.data('url')
                };
            }
        });

        localStorage.setItem(navSettingsKey, JSON.stringify(currentSettings));
        loadAndApplyNavSettings(); // Okamžitě aplikujeme změny
    }

    // Event listener pro změnu
    settingsContainer.on('change', 'input[type="radio"]', function() {
        saveNavSettings();
    });

    // Načteme nastavení při startu
    loadAndApplyNavSettings();
});