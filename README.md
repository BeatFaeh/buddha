# Buddhistische Lernkarten – modular

Die Anwendung speichert Lernkarten ausschließlich als **Frage + Antwort** in `tbl_buddhismus`.

## Multiple Choice

Multiple Choice wird **nicht separat gespeichert**. Der `QuizService` verwendet:

- die `antwort` der aktuellen Lernkarte als richtige Antwort;
- drei zufällige, unterschiedliche Antworten anderer Lernkarten als Distraktoren;
- eine zufällige Reihenfolge A–D.

Damit gibt es nur eine Datenwahrheit.

## Bestehende Installation migrieren

Falls die Tabelle aus einer älteren Version noch folgende Spalten besitzt:

- `kartentyp`
- `option_a`
- `option_b`
- `option_c`
- `option_d`
- `richtige_option`

zuerst ein Datenbank-Backup erstellen und anschließend einmalig ausführen:

`database/migrations/001_remove_legacy_multiple_choice_columns.sql`

Danach bestehen Lernkarten nur noch aus `frage` und `antwort` (plus technischen Zeitstempeln/ID).

## Architektur

- `index.php`: Routing
- `bootstrap/`: Initialisierung
- `config/`: Konfiguration
- `database/`: Schema, Seed und Migrationen
- `src/Repository/`: Datenzugriff
- `src/Service/QuizService.php`: automatische Multiple-Choice-Erzeugung
- `src/Security/`: Authentifizierung und CSRF
- `src/Support/`: Hilfsfunktionen
- `actions/`: schreibende Aktionen
- `pages/`: Ansichten
- `partials/`: wiederverwendbare HTML-Bausteine
- `assets/css/`: Stylesheets
- `assets/js/quiz.js`: Richtig/Falsch-Auswertung
