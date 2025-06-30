document.addEventListener('DOMContentLoaded', function() {
    console.log('CUSTOM MENU SCRIPT (PHP RENDERED): DOMContentLoaded fired.');

    var hamburgerIcon = document.getElementById('custom-hamburger-trigger');
    var customSideMenu = document.getElementById('custom-side-menu');
    var closeButton = document.getElementById('custom-menu-close-btn');
    var overlay = document.getElementById('mobile-menu-overlay');

    // Check if the elements were found (they should be, as PHP generates them)
    if (!hamburgerIcon) console.error('CUSTOM MENU SCRIPT: Hamburger icon #custom-hamburger-trigger NOT FOUND in DOM.');
    if (!customSideMenu) console.error('CUSTOM MENU SCRIPT: Side menu #custom-side-menu NOT FOUND in DOM.');
    if (!closeButton) console.error('CUSTOM MENU SCRIPT: Close button #custom-menu-close-btn NOT FOUND in DOM.');
    if (!overlay) console.error('CUSTOM MENU SCRIPT: Overlay #mobile-menu-overlay NOT FOUND in DOM.');

    // --- 5. Open/close functionality ---
    function openMenu() {
        if (customSideMenu && overlay && hamburgerIcon) {
            customSideMenu.classList.add('active');
            customSideMenu.setAttribute('aria-hidden', 'false');
            overlay.classList.add('active');
            document.body.classList.add('custom-menu-open');
            hamburgerIcon.setAttribute('aria-expanded', 'true');
            hamburgerIcon.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>';
            hamburgerIcon.classList.add('is-hidden-while-menu-open'); // << ADDED LINE
            console.log('CUSTOM MENU SCRIPT: Menu opened.');
        } else {
            console.warn('CUSTOM MENU SCRIPT: Could not open menu, one or more critical elements missing (menu, overlay, or icon).');
        }
    }

    function closeMenu() {
        if (customSideMenu && overlay && hamburgerIcon) {
            customSideMenu.classList.remove('active');
            customSideMenu.setAttribute('aria-hidden', 'true');
            overlay.classList.remove('active');
            document.body.classList.remove('custom-menu-open');
            hamburgerIcon.setAttribute('aria-expanded', 'false');
            hamburgerIcon.innerHTML = '<i class="fa fa-bars" aria-hidden="true"></i>';
            hamburgerIcon.classList.remove('is-hidden-while-menu-open'); // << ADDED LINE
            console.log('CUSTOM MENU SCRIPT: Menu closed.');
        } else {
            console.warn('CUSTOM MENU SCRIPT: Could not close menu, one or more critical elements missing.');
        }
    }

    if (hamburgerIcon && customSideMenu && closeButton && overlay) {
        hamburgerIcon.addEventListener('click', function(event) {
            event.preventDefault();
            if (customSideMenu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        closeButton.addEventListener('click', closeMenu);
        overlay.addEventListener('click', closeMenu);
        console.log('CUSTOM MENU SCRIPT: Event listeners attached.');
    } else {
        console.error('CUSTOM MENU SCRIPT: One or more essential elements for menu interaction are missing. Listeners NOT attached.');
    }

    console.log('CUSTOM MENU SCRIPT (PHP RENDERED): Script execution finished.');
});