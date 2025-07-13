jQuery(document).ready(function($) {
    "use strict";

    const settingsContainer = $('#life-situation-settings');
    if (!settingsContainer.length) {
        return; 
    }

    // --- Logika pro akordeon ---
    settingsContainer.on('click', '.accordion-btn', function() {
        const content = $(this).next('.accordion-content');
        
        if ($(this).hasClass('active')) {
            // Zavřít aktuální
            content.css('max-height', '0');
            $(this).removeClass('active');
        } else {
            // Zavřít ostatní
            $('.accordion-content').css('max-height', '0');
            $('.accordion-btn').removeClass('active');
            
            // Otevřít aktuální
            $(this).addClass('active');
            // Nastaví max-height podle skutečného obsahu pro plynulou animaci
            content.css('max-height', content.get(0).scrollHeight + 'px');
        }
    });

    const storageKey = 'pehobr_life_situations';

    // --- Funkce pro uložení nastavení ---
    function saveSituations() {
        const situations = {};
        settingsContainer.find('.situation-toggle').each(function() {
            const key = $(this).data('situation-key');
            if (key) {
                situations[key] = $(this).is(':checked');
            }
        });
        localStorage.setItem(storageKey, JSON.stringify(situations));
    }

    // --- Funkce pro načtení nastavení ---
    function loadSituations() {
        const savedData = localStorage.getItem(storageKey);
        if (savedData) {
            try {
                const situations = JSON.parse(savedData);
                for (const [key, isChecked] of Object.entries(situations)) {
                    settingsContainer.find('#toggle-' + key).prop('checked', isChecked);
                }
            } catch (e) {
                console.error('Chyba při parsování uložených životních situací:', e);
                localStorage.removeItem(storageKey); // Odstraní poškozená data
            }
        }
    }

    // Při změně jakéhokoli přepínače uložíme nové nastavení
    settingsContainer.on('change', '.situation-toggle', saveSituations);

    // Při načtení stránky načteme uložená data
    loadSituations();
});