jQuery(document).ready(function($) {
    // Cílíme pouze na menu v bočním panelu na desktopu
    const sidebarMenu = $('#secondary .widget_nav_menu');

    // Najdeme všechny položky, které mají pod-menu
    const parentMenuItems = sidebarMenu.find('.menu-item-has-children');

    // Projdeme každou takovou položku a přidáme jí třídu pro stylování
    parentMenuItems.each(function() {
        const listItem = $(this);
        const link = listItem.children('a');
        
        // Přidáme šipku pomocí HTML, aby byla lépe ovladatelná
        link.append('<span class="submenu-arrow"></span>');

        // Skryjeme pod-menu na začátku
        listItem.children('ul.sub-menu').hide();

        // Přidáme posluchač kliknutí
        link.on('click', function(event) {
            // Zabráníme přechodu na odkaz, pokud má položka pod-menu
            event.preventDefault();
            
            // Najdeme nejbližší pod-menu a plynule ho vysuneme/zasuneme
            const subMenu = listItem.children('ul.sub-menu');
            subMenu.slideToggle(200);

            // Přepneme třídu pro "otevřený" stav, což otočí šipku
            listItem.toggleClass('submenu-is-open');
        });
    });
});