jQuery(document).ready(function($) {
    "use strict";

    const settingsContainer = $('#right-menu-settings');
    const rightMenuPanel = $('#mobile-menu-panel .mobile-menu-ul'); // Cílíme na menu panel
    const storageKey = 'pehobr_right_menu_visibility';

    // Funkce pro uložení nastavení
    function saveSettings() {
        const visibility = {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('menu-slug');
            visibility[slug] = $(this).is(':checked') ? 'visible' : 'hidden';
        });
        localStorage.setItem(storageKey, JSON.stringify(visibility));
        applyMenuVisibility(); // Okamžitě aplikujeme změny
    }

    // Funkce, která aplikuje viditelnost na položky menu
    function applyMenuVisibility() {
        if (!rightMenuPanel.length) return;

        const visibility = JSON.parse(localStorage.getItem(storageKey)) || {};

        // Projdeme všechny položky menu
        rightMenuPanel.find('li').each(function() {
            const menuItem = $(this);
            const link = menuItem.find('a');
            const href = link.attr('href');
            let isVisible = true; // Výchozí stav je viditelný

            if (href) {
                // Projdeme nastavení a zkontrolujeme, jestli URL odpovídá skrytému slugu
                for (const [slug, status] of Object.entries(visibility)) {
                    if (status === 'hidden' && href.includes('/' + slug + '/')) {
                        isVisible = false;
                        break; // Není třeba dál hledat
                    }
                }
            }
            
            // Aplikujeme viditelnost
            menuItem.toggle(isVisible);
        });
    }

    // Funkce, která načte nastavení a nastaví přepínače na stránce s nastavením
    function loadSettingsForPage() {
        if (!settingsContainer.length) return;

        const visibility = JSON.parse(localStorage.getItem(storageKey)) || {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('menu-slug');
            // Pokud záznam neexistuje, je ve výchozím stavu viditelný (zaškrtnutý)
            const isVisible = (visibility[slug] === 'hidden') ? false : true;
            $(this).prop('checked', isVisible);
        });
    }

    // --- Event Listeners & Incializace ---

    // Pokud jsme na stránce s nastavením, navážeme listener a načteme stavy přepínačů
    if (settingsContainer.length) {
        settingsContainer.on('change', '.visibility-toggle', saveSettings);
        loadSettingsForPage();
    }

    // Aplikujeme viditelnost na menu při každém načtení stránky
    applyMenuVisibility();

});