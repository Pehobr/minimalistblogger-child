jQuery(document).ready(function($) {
    "use strict";

    const container = $('#ai-inspiration-container');
    if (!container.length) return;

    const btn = $('#generate-inspiration-btn');
    const resultDiv = $('#inspiration-result');
    const loader = resultDiv.find('.loader');
    const textDiv = resultDiv.find('.inspiration-text');
    const scriptureDisplay = $('#daily-scripture-display .scripture-content');

    // --- 1. Načtení denního čtení ---
    // Načteme celý obsah ze stránky s liturgickým čtením
    scriptureDisplay.load('/poboznosti/ .entry-content', function(response, status, xhr) {
        if (status === "error") {
            scriptureDisplay.html("<p>Chyba při načítání denního čtení. Zkuste to prosím později.</p>");
            btn.prop('disabled', true).text('Chyba při načítání dat');
        } else {
            // Úspěšně načteno - tlačítko je nyní aktivní
            btn.prop('disabled', false); 
        }
    });

    // --- 2. Po kliknutí na tlačítko ---
    btn.on('click', function() {
        
        // --- 2a. Získání textu denního čtení ---
        // Vytvoříme dočasný element, abychom mohli snadno odstranit nechtěné části
        const tempDiv = $('<div>').html(scriptureDisplay.html());
        tempDiv.find('h1, h2, h3').remove(); // Odstraníme všechny nadpisy
        const scriptureText = tempDiv.text().trim();

        if (!scriptureText) {
            alert('Text denního čtení je prázdný. Zkuste prosím obnovit stránku.');
            return;
        }

        // --- 2b. Získání životní situace z localStorage ---
        const lifeSituationsKey = 'pehobr_life_situations';
        const savedSituationsData = localStorage.getItem(lifeSituationsKey);
        const savedSituations = savedSituationsData ? JSON.parse(savedSituationsData) : {};
        
        const activeSituations = Object.keys(savedSituations).filter(key => savedSituations[key]);

        if (activeSituations.length === 0) {
            // Použijeme confirm dialog, aby uživatel mohl volbu zrušit
            if (!confirm('Pro lepší výsledky si nejprve nastavte vaši životní situaci. Chcete přesto pokračovat s obecným textem?')) {
                return; // Pokud uživatel klikne na "Storno", přerušíme akci
            }
        }
        
        // Převedeme klíče na čitelný text
        const situationsString = activeSituations.join(', ').replace(/_/g, ' ');

        // --- 2c. Zobrazení loaderu a příprava na výsledek ---
        resultDiv.show();
        textDiv.hide();
        loader.show();
        btn.prop('disabled', true).text('Generuji inspiraci...');

        // --- 2d. Odeslání dat na server pomocí AJAX ---
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'generate_ai_inspiration',
                situations: situationsString,
                scripture: scriptureText
            },
            success: function(response) {
                if (response.success) {
                    const formattedText = response.data.inspiration.replace(/\n/g, '<br>');
                    textDiv.html(formattedText).css('text-align', 'left'); // Zarovnání textu vlevo pro lepší čitelnost
                } else {
                    textDiv.html('<p class="error">Omlouváme se, nastala chyba při generování textu. Zkuste to prosím znovu později.</p>');
                    console.error('Chyba od serveru:', response.data);
                }
            },
            error: function(xhr) {
                textDiv.html('<p class="error">Omlouváme se, nastala kritická chyba při komunikaci se serverem.</p>');
                console.error('AJAX chyba:', xhr);
            },
            complete: function() {
                loader.hide();
                textDiv.show();
                btn.prop('disabled', false).text('Vygenerovat novou inspiraci');
            }
        });
    });
});