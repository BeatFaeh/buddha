<?php
declare(strict_types=1);

$search = trim((string) ($_GET['suche'] ?? ''));
$cards = $search !== '' ? $cardRepository->search($search) : $cardRepository->allAscending();
$glossary = $glossaryRepository->asMap();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buddhistische Lernkarten – Druckansicht</title>
    <link rel="stylesheet" href="assets/css/print.css">
</head>
<body>
<div class="toolbar">
    <form class="search-form" method="get" action="index.php">
        <input type="hidden" name="action" value="pdf">
        <input type="search" name="suche" value="<?= Html::e($search) ?>" placeholder="Fragen und Antworten durchsuchen …">
        <button type="submit">Suchen</button>
        <a class="button neutral" href="index.php?action=pdf">Zurücksetzen</a>
    </form>
    <div class="print-actions">
        <button type="button" onclick="window.print()">Drucken / Als PDF speichern</button>
        <a class="button secondary" href="index.php">Zurück zu den Lernkarten</a>
    </div>
    <p class="result-info">
        <?= count($cards) ?> <?= count($cards) === 1 ? 'Lernkarte' : 'Lernkarten' ?><?= $search !== '' ? ' für „' . Html::e($search) . '“' : ' insgesamt' ?>
    </p>
</div>

<main class="document">
    <h1>Buddhistische Lernkarten</h1>
    <p class="subtitle"><?= $search !== '' ? 'Gefilterte Ausgabe' : 'Gesamtausgabe' ?></p>

    <?php foreach ($cards as $card): ?>
        <section class="learning-card">
            <div class="number">Lernkarte #<?= (int) $card['id'] ?></div>
            <div class="question"><?= Html::e($card['frage']) ?></div>
            <div class="answer"><?= $glossaryFormatter->format($card['antwort'], $glossary) ?></div>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>
