<?php
declare(strict_types=1);

final class GlossaryFormatter
{
    public function format(?string $text, array $glossary): string
    {
        $safeText = Html::e($text);

        foreach ($glossary as $term => $explanation) {
            $pattern = '/(?<![\\p{L}\\p{N}])' . preg_quote((string)$term, '/') . '(?![\\p{L}\\p{N}])/u';
            $safeText = preg_replace_callback(
                $pattern,
                static function (array $matches) use ($explanation): string {
                    return '<span class="term-tooltip" tabindex="0" data-tooltip="'
                        . Html::e((string)$explanation) . '">' . $matches[0] . '</span>';
                },
                $safeText
            ) ?? $safeText;
        }

        return nl2br($safeText);
    }
}
