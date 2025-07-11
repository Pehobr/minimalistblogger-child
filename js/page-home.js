document.addEventListener('DOMContentLoaded', function() {
    const settings = JSON.parse(localStorage.getItem('pehobr_user_settings')) || {};

    /**
     * Funkce pro aplikaci režimu zobrazení (grafické/textové) pro danou sekci.
     * @param {string} containerSelector - Selektor hlavního kontejneru sekce (např. '.pope-section-container').
     * @param {string} settingKey - Klíč v localStorage (např. 'pope_section_display').
     */
    function applyDisplayMode(containerSelector, settingKey) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        // Načte preferenci uživatele, nebo použije výchozí hodnotu zadanou v PHP
        const defaultView = container.dataset.defaultView || 'graficke';
        const userPreference = (settings.display && settings.display[settingKey]) ? settings.display[settingKey] : defaultView;

        const graphicalView = container.querySelector('.view-graficke');
        const textView = container.querySelector('.view-textove');

        if (!graphicalView || !textView) return;

        // Aplikuje správný styl zobrazení
        if (userPreference === 'textove') {
            graphicalView.style.display = 'none';
            textView.style.display = 'block'; // Zobrazí textovou variantu
        } else {
            graphicalView.style.display = 'flex'; // Zobrazí grafickou variantu
            textView.style.display = 'none';
        }
    }

    /**
     * Funkce pro aplikaci viditelnosti sekcí podle uživatelského nastavení.
     */
    function applyVisibility() {
        if (!settings.visibility) return;

        const sectionMap = {
            pope_section: '.pope-section-container',
            saints_section: '.saints-section-container',
            actions_section: '.third-row-section-container',
            desktop_nav_section: '#desktop-nav-grid-container',
            library_section: '#library-grid-container'
        };

        for (const slug in settings.visibility) {
            if (settings.visibility[slug] === false) {
                const selector = sectionMap[slug];
                const sectionElement = selector ? document.querySelector(selector) : null;
                if (sectionElement) {
                    sectionElement.style.display = 'none';
                }
            }
        }
    }

    // --- INICIALIZACE VZHLEDU STRÁNKY ---

    // 1. Aplikujeme viditelnost jednotlivých sekcí
    applyVisibility();

    // 2. Aplikujeme režim zobrazení (grafický/textový) pro obě relevantní sekce
    applyDisplayMode('.pope-section-container', 'pope_section_display');
    applyDisplayMode('.saints-section-container', 'saints_section_display');


    // --- Logika pro modální okna, lightbox atd. ---
    const modalOverlay = document.getElementById('quote-modal-overlay');
    const modalContainer = document.getElementById('quote-modal-container');
    const modalContent = document.getElementById('quote-modal-content');
    const modalCloseBtn = document.getElementById('quote-modal-close-btn');

    document.querySelectorAll('.pope-icon-link, .icon-grid-item').forEach(link => {
        // Kontrolujeme, zda prvek má data-target-id, což indikuje, že má otevírat modální okno
        if (link.dataset.targetId) {
            link.addEventListener('click', function(e) {
                // Zabráníme výchozí akci pouze pokud je href nastaven na '#'
                if (link.getAttribute('href') === '#') {
                    e.preventDefault();
                }

                const contentElement = document.getElementById(link.dataset.targetId);
                if (contentElement && modalContent && modalOverlay && modalContainer) {
                    modalContent.innerHTML = contentElement.innerHTML;
                    modalOverlay.style.display = 'block';
                    modalContainer.style.display = 'block';
                    // Případná inicializace audio přehrávače v modálním okně
                    const audioPlayerDiv = modalContent.querySelector('.modal-audio-player');
                    if(audioPlayerDiv && audioPlayerDiv.dataset.audioSrc) {
                         // Zde by přišla logika pro vytvoření a spuštění audio elementu, pokud je potřeba
                    }
                }
            });
        }
    });

    const closeModal = () => {
        if (modalOverlay && modalContainer) {
            modalOverlay.style.display = 'none';
            modalContainer.style.display = 'none';
            if (modalContent) modalContent.innerHTML = ''; // Vyčistíme obsah
        }
    };

    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
    if (modalOverlay) modalOverlay.addEventListener('click', closeModal);

    // Inicializace Lightboxu pro obrázky, pokud je knihovna dostupná
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true
        });
    }

    // Donation popup logika (pokud existuje)
    const donationContainer = document.getElementById('donation-popup-container');
    if (donationContainer && typeof donation_popup_settings !== 'undefined' && donation_popup_settings.show_popup) {
        const timerSpan = document.getElementById('donation-timer');
        let countdown = 7;
        const interval = setInterval(() => {
            countdown--;
            if (timerSpan) timerSpan.textContent = countdown + 's';
            if (countdown <= 0) {
                clearInterval(interval);
                if (donationContainer) donationContainer.style.display = 'none';
                if (document.getElementById('donation-popup-overlay')) {
                    document.getElementById('donation-popup-overlay').style.display = 'none';
                }
            }
        }, 1000);
    }
});