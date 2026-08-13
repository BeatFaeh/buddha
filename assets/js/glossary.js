'use strict';

document.addEventListener('DOMContentLoaded', () => {

    const searchInput = document.getElementById('glossary-search');
    const sortSelect = document.getElementById('glossary-sort');
    const list = document.getElementById('glossary-list');

    if (!searchInput || !sortSelect || !list) {
        return;
    }

    const entries = Array.from(
        list.querySelectorAll('.entry')
    );

    function updateGlossary() {

        const searchText = searchInput.value
            .trim()
            .toLocaleLowerCase('de');

        const sortDirection = sortSelect.value;

        /*
         * 1. Sortieren
         */
        const sortedEntries = [...entries].sort((a, b) => {

            const termA = a.dataset.term ?? '';
            const termB = b.dataset.term ?? '';

            const comparison = termA.localeCompare(
                termB,
                'de',
                {
                    sensitivity: 'base',
                    numeric: true
                }
            );

            return sortDirection === 'desc'
                ? -comparison
                : comparison;
        });

        /*
         * 2. DOM neu anordnen
         */
        sortedEntries.forEach(entry => {
            list.appendChild(entry);
        });

        /*
         * 3. Suche anwenden
         */
        sortedEntries.forEach(entry => {

            const searchableText =
                entry.dataset.search ?? '';

            const visible =
                searchableText.includes(searchText);

            entry.hidden = !visible;
        });
    }

    searchInput.addEventListener(
        'input',
        updateGlossary
    );

    sortSelect.addEventListener(
        'change',
        updateGlossary
    );

    /*
     * Beim Laden bereits A–Z sortieren.
     */
    updateGlossary();

});