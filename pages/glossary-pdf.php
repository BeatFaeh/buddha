<?php

declare(strict_types=1);

$search = trim((string) ($_GET['suche'] ?? ''));
$printDate = (new DateTimeImmutable('now', new DateTimeZone('Europe/Zurich')))->format('d.m.Y');
$rows = $glossaryRepository->all();

if ($search !== '') {
    $needle = mb_strtolower($search, 'UTF-8');
    $rows = array_values(array_filter(
        $rows,
        static function (array $row) use ($needle): bool {
            $haystack = mb_strtolower(
                (string) ($row['begriff'] ?? '') . ' ' . (string) ($row['erklaerung'] ?? ''),
                'UTF-8'
            );
            return mb_strpos($haystack, $needle, 0, 'UTF-8') !== false;
        }
    ));
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buddhistisches Glossar – Druckansicht</title>
    <link rel="stylesheet" href="assets/css/print.css">
</head>
<body>
<div class="toolbar">
    <form class="search-form" method="get" action="index.php">
        <input type="hidden" name="action" value="glossar-pdf">
        <input type="search" name="suche" value="<?= Html::e($search) ?>" placeholder="Glossar durchsuchen …">
        <button type="submit">Suchen</button>
        <a class="button neutral" href="index.php?action=glossar-pdf">Zurücksetzen</a>
    </form>

    <div class="print-actions">
        <button type="button" onclick="window.print()">Drucken / Als PDF speichern</button>
        <a class="button secondary" href="index.php?action=glossar">Zurück zum Glossar</a>
    </div>

    <p class="result-info">
        <?= count($rows) ?> <?= count($rows) === 1 ? 'Begriff' : 'Begriffe' ?><?= $search !== '' ? ' für „' . Html::e($search) . '“' : ' insgesamt' ?>
    </p>
</div>

<main class="document glossary-document" style="--print-date: '<?= Html::e($printDate) ?>';">
    <header class="document-header">
        <div>
            <h1>Buddhistisches Glossar</h1>
            <p class="subtitle"><?= $search !== '' ? 'Gefilterte Ausgabe' : 'Alphabetische Gesamtausgabe' ?></p>
        </div>
        <p class="document-date">Datum: <?= Html::e($printDate) ?></p>
    </header>

    <?php if ($rows === []): ?>
        <p>Keine Glossarbegriffe gefunden.</p>
    <?php else: ?>
        <?php foreach ($rows as $row): ?>
            <section class="glossary-print-entry">
                <div class="glossary-term"><?= Html::e((string) $row['begriff']) ?></div>
                <div class="glossary-explanation"><?= nl2br(Html::e((string) $row['erklaerung'])) ?></div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>
