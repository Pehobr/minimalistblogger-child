/**
 * JavaScript pro stránku Zápisník (page-zapisnik.php)
 * VERZE 3: Upraven export a kopírování - pouze text, řazeno od nejstarší.
 */
jQuery(document).ready(function($) {

    // --- SELEKCE PRVKŮ ---
    const form = $('#zapisnik-form');
    const textarea = $('#zapisnik-textarea');
    const notesContainer = $('#zapisky-container');
    const showNotesBtn = $('#show-notes-btn');
    const exportBtn = $('#export-notes-btn');
    const copyAllBtn = $('#copy-all-notes-btn');

    const storageKey = 'pehobr_zapisnik_notes';

    // --- FUNKCE PRO PRÁCI S LOCALSTORAGE ---
    function getNotes() {
        const notesJSON = localStorage.getItem(storageKey);
        return notesJSON ? JSON.parse(notesJSON) : [];
    }

    function saveNotes(notes) {
        localStorage.setItem(storageKey, JSON.stringify(notes));
    }

    // --- FUNKCE PRO VYKRESLENÍ POZNÁMEK ---
    function renderNotes() {
        const notes = getNotes();
        notesContainer.empty();

        if (notes.length === 0) {
            notesContainer.html('<p>Zatím nemáte žádné uložené poznámky.</p>').hide();
             showNotesBtn.find('.fa').removeClass('fa-chevron-up').addClass('fa-history');
            return;
        }

        notes.forEach(note => {
            const noteItemHTML = `
                <div class="zapisnik-item" data-id="${note.id}">
                    <div class="zapisnik-content" contenteditable="false">${escapeHTML(note.text)}</div>
                    <div class="zapisnik-footer">
                        <div class="zapisnik-item-actions">
                            <button class="edit-btn" aria-label="Upravit poznámku"><i class="fa fa-pencil"></i></button>
                            <button class="delete-btn" aria-label="Smazat poznámku"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `;
            notesContainer.append(noteItemHTML);
        });
    }

    // --- FUNKCE PRO OVLÁDÁNÍ ---

    // Uložení nebo aktualizace poznámky
    form.on('submit', function(event) {
        event.preventDefault();
        const text = textarea.val().trim();
        if (!text) {
            alert('Nelze uložit prázdnou poznámku.');
            return;
        }

        let notes = getNotes();
        const noteIdToUpdate = textarea.data('editing-id');

        if (noteIdToUpdate) {
            const noteIndex = notes.findIndex(n => n.id == noteIdToUpdate);
            if (noteIndex > -1) {
                notes[noteIndex].text = text;
                notes[noteIndex].date = new Date().toISOString();
            }
            textarea.removeData('editing-id');
            $('#zapisnik-save-btn').html('<i class="fa fa-floppy-o"></i> Uložit poznámku');
        } else {
            const newNote = {
                id: Date.now(),
                text: text,
                date: new Date().toISOString()
            };
            notes.unshift(newNote); // Nová se stále přidává nahoru pro zobrazení v aplikaci
        }

        saveNotes(notes);
        textarea.val('');
        renderNotes();
        notesContainer.slideDown();
        showNotesBtn.find('.fa').removeClass('fa-history').addClass('fa-chevron-up');
    });

    // Zobrazení / Skrytí poznámek
    showNotesBtn.on('click', function() {
        const icon = $(this).find('.fa');
        if (notesContainer.is(':visible')) {
            notesContainer.slideUp();
            icon.removeClass('fa-chevron-up').addClass('fa-history');
        } else {
            renderNotes();
            notesContainer.slideDown();
            icon.removeClass('fa-history').addClass('fa-chevron-up');
        }
    });

    // Delegované události pro tlačítka u poznámek
    notesContainer.on('click', '.delete-btn', function() {
        if (!confirm('Opravdu chcete tuto poznámku smazat?')) return;

        const noteId = $(this).closest('.zapisnik-item').data('id');
        let notes = getNotes();
        notes = notes.filter(n => n.id != noteId);
        saveNotes(notes);
        renderNotes();
         if (notes.length === 0) {
            notesContainer.slideUp();
            showNotesBtn.find('.fa').removeClass('fa-chevron-up').addClass('fa-history');
        }
    });

    notesContainer.on('click', '.edit-btn', function() {
        const item = $(this).closest('.zapisnik-item');
        const noteId = item.data('id');
        const currentText = getNotes().find(n => n.id == noteId).text;

        textarea.val(currentText);
        textarea.data('editing-id', noteId);
        $('#zapisnik-save-btn').html('<i class="fa fa-check"></i> Uložit změny');
        textarea.focus();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });


    // Export do TXT
    exportBtn.on('click', function() {
        let notes = getNotes();
        if (notes.length === 0) {
            alert('Není co exportovat.');
            return;
        }

        // <<< ZMĚNA ZDE: Obrátíme pole, aby byly poznámky seřazeny od nejstarší
        notes.reverse();

        // <<< ZMĚNA ZDE: Spojíme pouze texty poznámek, oddělené dvěma novými řádky
        const textContent = notes.map(note => note.text).join('\n\n');

        const blob = new Blob([textContent], { type: 'text/plain;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'moje-zapisky.txt';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Kopírovat vše (pro mobily)
    copyAllBtn.on('click', function() {
        let notes = getNotes();
        if (notes.length === 0) {
            alert('Není co kopírovat.');
            return;
        }
        
        // <<< ZMĚNA ZDE: Obrátíme pole i zde
        notes.reverse();

        // <<< ZMĚNA ZDE: Stejný formát jako pro export
        const textContent = notes.map(note => note.text).join('\n\n');

        if (navigator.clipboard) {
            navigator.clipboard.writeText(textContent).then(() => {
                alert('Všechny poznámky byly zkopírovány do schránky.');
            }, () => {
                alert('Nepodařilo se zkopírovat poznámky.');
            });
        } else {
            alert('Váš prohlížeč nepodporuje automatické kopírování.');
        }
    });

    // Pomocná funkce pro escapování HTML
    function escapeHTML(str) {
        return $('<div>').text(str).html();
    }

    // Prvotní vykreslení při načtení (ale skryté)
    renderNotes();
});