document.addEventListener("DOMContentLoaded", function() {
    // Najde všechna tlačítka akordeonu na stránce
    var acc = document.getElementsByClassName("accordion");
    var i;

    // Projde všechna nalezená tlačítka
    for (i = 0; i < acc.length; i++) {
        // Přidá každému tlačítku posluchač události pro kliknutí
        acc[i].addEventListener("click", function() {
            /* Přepne třídu 'active', která slouží pro vizuální změny (např. barva, ikona šipky) */
            this.classList.toggle("active");

            /* Najde následující prvek (panel s obsahem) */
            var panel = this.nextElementSibling;

            /* Zkontroluje, zda má panel nastavenou výšku. Pokud ano (je otevřený),
               zruší ji (zavře ho). Pokud ne (je zavřený), nastaví jeho maximální výšku
               na výšku jeho obsahu (otevře ho). */
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }
});