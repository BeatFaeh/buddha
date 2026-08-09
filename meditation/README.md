# Buddhistischer Verzeichnis-Browser

Dieser kleine PHP-Browser zeigt automatisch alle Dateien im Verzeichnis an,
in dem sich `index.php` befindet.

## Verwendung

Kopiere diese drei Dateien in ein beliebiges Verzeichnis:

- `index.php`
- `style.css`
- `directory.js`

Beispiele:

```text
/literatur/
    index.php
    style.css
    directory.js
    Das_Herz_von_Buddhas_Lehre.pdf
    Lama_Govinda.docx

/lernmodule/
    index.php
    style.css
    directory.js
    Modul_01.pdf
    Modul_02.pdf
    Modul_03.pdf
```

Der Seitentitel wird automatisch aus dem Ordnernamen gebildet.

`lernmodule` wird beispielsweise zu **Lernmodule**.

## Eigener Titel

Oben in `index.php`:

```php
$pageTitle = '';
```

kann ein eigener Titel eingetragen werden:

```php
$pageTitle = 'Meine Literatur';
```

## Funktionen

- Look & Feel analog zu den buddhistischen Lernkarten
- automatische Dateiliste
- Suche
- Sortierung nach Dateiname, Typ, Grösse und Änderungsdatum
- Dateityp-Erkennung
- Dateigrösse
- Änderungsdatum
- responsive Darstellung für Mobilgeräte
- HTML-Ausgabe wird escaped
- Dateinamen werden URL-sicher verlinkt
- technische Dateien werden automatisch ausgeblendet

Das alte `sorttable.js` wird nicht mehr benötigt.
