document.addEventListener('DOMContentLoaded', function() {
    // Pokusí se načíst existující nastavení z localStorage, nebo vytvoří prázdný objekt
    const settings = JSON.parse(localStorage.getItem('pehobr_user_settings')) || {};

    // --- Přepínače viditelnosti sekcí ---
    document.querySelectorAll('.visibility-toggle').forEach(toggle => {
        const slug = toggle.dataset.sectionSlug;

        // Inicializace stavu přepínače podle uložených dat
        if (settings.visibility && settings.visibility[slug] !== undefined) {
            toggle.checked = settings.visibility[slug];
        } else {
            // Výchozí stav, pokud nastavení ještě neexistuje (vše je viditelné)
            toggle.checked = true;
        }

        // Uložení změny do localStorage při každém přepnutí
        toggle.addEventListener('change', function() {
            if (!settings.visibility) settings.visibility = {};
            settings.visibility[slug] = this.checked;
            localStorage.setItem('pehobr_user_settings', JSON.stringify(settings));
        });
    });

    // --- Přepínače režimu zobrazení (Grafické vs. Textové) ---
    document.querySelectorAll('.display-toggle').forEach(toggle => {
        const slug = toggle.dataset.sectionSlug; // např. 'pope_section_display'

        // Inicializace stavu podle uložených dat
        if (settings.display && settings.display[slug] !== undefined) {
            toggle.checked = (settings.display[slug] === 'textove');
        } else {
             // Výchozí stav (grafické)
            toggle.checked = false;
        }

        // Uložení změny do localStorage
        toggle.addEventListener('change', function() {
            if (!settings.display) settings.display = {};
            const displayMode = this.checked ? 'textove' : 'graficke';
            settings.display[slug] = displayMode;
            localStorage.setItem('pehobr_user_settings', JSON.stringify(settings));
        });
    });
});