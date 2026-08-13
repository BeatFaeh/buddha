<?php

declare(strict_types=1);

final class GlossaryFormatter
{
    public function format(?string $text, array $glossary): string
    {
        /*
         * Ausgangstext HTML-sicher machen.
         */
        $safeText = Html::e((string) $text);

        if ($safeText === '' || $glossary === []) {
            return nl2br($safeText);
        }

        /*
         * Alle Glossarbegriffe in EINEM regulären Ausdruck
         * zusammenfassen.
         *
         * Dadurch wird der ursprüngliche Text nur einmal
         * verarbeitet. Bereits erzeugtes Tooltip-HTML wird
         * nicht erneut durchsucht.
         */
        $terms = array_keys($glossary);

        /*
         * Längere Begriffe zuerst.
         *
         * Beispiel:
         * "Vier Edle Wahrheiten" soll vor "Wahrheit"
         * erkannt werden.
         */
        usort(
            $terms,
            static fn(string $a, string $b): int =>
                mb_strlen($b, 'UTF-8') <=> mb_strlen($a, 'UTF-8')
        );

        /*
         * Sonderzeichen für regulären Ausdruck maskieren.
         */
        $escapedTerms = array_map(
            static fn(string $term): string =>
            preg_quote($term, '/'),
            $terms
        );

        /*
         * Ein einziger Suchausdruck für alle Glossarbegriffe.
         */
        $pattern =
            '/(?<![\p{L}\p{N}])('
            . implode('|', $escapedTerms)
            . ')(?![\p{L}\p{N}])/u';

        /*
         * Glossarbegriffe mit Tooltip versehen.
         */
        $formatted = preg_replace_callback(
            $pattern,
            static function (array $matches) use ($glossary): string {

                $term = $matches[1];

                $explanation =
                    $glossary[$term] ?? '';

                return
                    '<span class="term-tooltip"'
                    . ' tabindex="0"'
                    . ' data-tooltip="'
                    . Html::e((string) $explanation)
                    . '">'
                    . $term
                    . '</span>';
            },
            $safeText
        );

        return nl2br(
            $formatted ?? $safeText
        );
    }
}