jQuery(function($) {
    // Počkáme, až se načtou obrázky a obsah
    $(window).on('load', function() {
        // Inicializace Isotope
        var $grid = $('#citat-list').isotope({
            itemSelector: '.citat-item',
            layoutMode: 'vertical' // Uspořádání pod sebe
        });

        // Po kliknutí na filtrační tlačítko
        $('#citat-filters').on('click', 'button', function() {
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({ filter: filterValue });

            // Aktualizace aktivního tlačítka
            $('#citat-filters button').removeClass('active');
            $(this).addClass('active');
        });
    });
});