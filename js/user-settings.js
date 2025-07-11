jQuery(document).ready(function($) {
    const settingsContainer = $('#user-layout-settings');
    if (!settingsContainer.length) {
        return; // Skript se nespustí, pokud na stránce není kontejner s nastavením
    }

    const storageKey = 'pehobr_user_home_visibility';

    // Funkce pro načtení uložených nastavení z localStorage
    function loadVisibilitySettings() {
        const savedSettings = localStorage.getItem(storageKey);
        // Výchozí nastavení je, že vše je viditelné ('on')
        const visibility = savedSettings ? JSON.parse(savedSettings) : {};

        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('section-slug');
            // Pokud pro daný slug není žádné nastavení, považujeme ho za zapnuté
            const isVisible = (visibility[slug] === 'on' || typeof visibility[slug] === 'undefined');
            $(this).prop('checked', isVisible);
        });
    }

    // Funkce pro uložení aktuálního nastavení do localStorage
    function saveVisibilitySettings() {
        const visibility = {};
        settingsContainer.find('.visibility-toggle').each(function() {
            const slug = $(this).data('section-slug');
            const isChecked = $(this).is(':checked');
            visibility[slug] = isChecked ? 'on' : 'off';
        });
        localStorage.setItem(storageKey, JSON.stringify(visibility));
    }

    // Při změně jakéhokoli přepínače uložíme nové nastavení
    settingsContainer.on('change', '.visibility-toggle', function() {
        saveVisibilitySettings();
    });

    // Při načtení stránky načteme a aplikujeme uložená nastavení
    loadVisibilitySettings();
});