/**
 * JavaScript pro vysouvací mobilní menu.
 */
jQuery(document).ready(function($) {

    // 1. Vytvoření ikony a její vložení do hlavičky
    // Cílíme na .site-branding, což je box s názvem aplikace
    const menuIconHTML = '<button id="menu-open-btn" aria-label="Otevřít menu"><i class="fa fa-bars"></i></button>';
    $('.site-branding').first().css('position', 'relative').prepend(menuIconHTML);

    // 2. Selekce všech potřebných prvků menu
    const menuPanel = $('#mobile-menu-panel');
    const menuOverlay = $('#mobile-menu-main-overlay');
    const openBtn = $('#menu-open-btn');
    const closeBtn = $('#mobile-menu-close-btn');

    // 3. Funkce pro otevírání a zavírání panelu
    function openMobileMenu() {
        menuPanel.addClass('is-open');
        menuOverlay.addClass('is-visible');
    }

    function closeMobileMenu() {
        menuPanel.removeClass('is-open');
        menuOverlay.removeClass('is-visible');
    }

    // 4. Přidání posluchačů událostí (Event Listeners)
    openBtn.on('click', openMobileMenu);
    closeBtn.on('click', closeMobileMenu);
    menuOverlay.on('click', closeMobileMenu);
    
    // Zavření menu po kliknutí na odkaz uvnitř
    menuPanel.on('click', 'a', function() {
        closeMobileMenu();
    });

    // Zavření pomocí klávesy Escape
    $(document).on('keydown', function(event) {
        if (event.key === "Escape" && menuPanel.hasClass('is-open')) {
            closeMobileMenu();
        }
    });
});