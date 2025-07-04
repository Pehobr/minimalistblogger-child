/**
 * JavaScript pro vysouvací LEVÉ mobilní menu.
 */
jQuery(document).ready(function($) {

    // 1. Vytvoření ikony a její vložení do hlavičky
    const menuIconHTML = '<button id="left-menu-open-btn" aria-label="Otevřít levé menu"><i class="fa fa-bars"></i></button>';
    $('.site-branding').first().css('position', 'relative').prepend(menuIconHTML);

    // 2. Selekce všech potřebných prvků levého menu
    const menuPanel = $('#left-mobile-menu-panel');
    const menuOverlay = $('#left-mobile-menu-overlay');
    const openBtn = $('#left-menu-open-btn');
    const closeBtn = $('#left-mobile-menu-close-btn');

    // 3. Funkce pro otevírání a zavírání panelu
    function openLeftMobileMenu() {
        menuPanel.addClass('is-open');
        menuOverlay.addClass('is-visible');
    }

    function closeLeftMobileMenu() {
        menuPanel.removeClass('is-open');
        menuOverlay.removeClass('is-visible');
    }

    // 4. Přidání posluchačů událostí (Event Listeners)
    openBtn.on('click', openLeftMobileMenu);
    closeBtn.on('click', closeLeftMobileMenu);
    menuOverlay.on('click', closeLeftMobileMenu);
    
    // Zavření menu po kliknutí na odkaz uvnitř
    menuPanel.on('click', 'a', function() {
        closeLeftMobileMenu();
    });

    // Zavření pomocí klávesy Escape
    $(document).on('keydown', function(event) {
        if (event.key === "Escape" && menuPanel.hasClass('is-open')) {
            closeLeftMobileMenu();
        }
    });
});