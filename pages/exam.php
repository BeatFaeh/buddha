<?php
declare(strict_types=1);

$exam = $examService->build(100);
$questions = $exam['questions'];
$total = $exam['count'];
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prüfung – 100 Multiple-Choice-Fragen</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/exam.css">
</head>
<body>
<main class="exam-page">
    <div class="exam-wrapper">
        <?php require __DIR__ . '/../partials/public-hero.php'; ?>

        <section class="exam-intro">
            <div>
                <p class="question-label">Wissenstest</p>
                <h2>100 zufällige Multiple-Choice-Fragen</h2>
                <p>
                    Beantworte jede Frage. Pro Frage gibt es genau eine richtige Antwort.
                    Die Auswertung erscheint erst am Schluss.
                </p>
            </div>
            <div class="exam-progress-box">
                <strong id="answered-count">0</strong> / <?= $total ?> beantwortet
            </div>
        </section>

        <?php if ($total === 0): ?>
            <section class="learning-card">
                <div class="card-content">
                    <h2>Keine Prüfungsfragen verfügbar</h2>
                    <p>Es werden mindestens vier unterschiedliche Antworten in der Datenbank benötigt.</p>
                    <a class="button button-secondary" href="index.php">← Zurück</a>
                </div>
            </section>
        <?php else: ?>
            <?php if ($total < 100): ?>
                <div class="exam-notice">
                    In der Datenbank konnten aktuell nur <?= $total ?> auswertbare Fragen erzeugt werden.
                </div>
            <?php endif; ?>

            <form id="exam-form" autocomplete="off">
                <?php foreach ($questions as $index => $question): ?>
                    <section
                        class="exam-question"
                        data-question="<?= $index + 1 ?>"
                        data-correct="<?= Html::e($question['correct_key']) ?>"
                    >
                        <div class="exam-question-head">
                            <span class="badge">Frage <?= $index + 1 ?> von <?= $total ?></span>
                            <span class="exam-status" aria-live="polite">Noch nicht beantwortet</span>
                        </div>

                        <h2><?= Html::e($question['frage']) ?></h2>

                        <div class="exam-options">
                            <?php foreach ($question['options'] as $letter => $answer): ?>
                                <label class="exam-option">
                                    <input
                                        type="radio"
                                        name="question_<?= $index + 1 ?>"
                                        value="<?= Html::e($letter) ?>"
                                    >
                                    <span class="mc-letter"><?= Html::e($letter) ?></span>
                                    <span class="exam-answer"><?= Html::e($answer) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>

                <section class="exam-submit-card">
                    <p>
                        <strong id="remaining-count"><?= $total ?></strong>
                        Fragen sind noch unbeantwortet.
                    </p>

                    <button
                        type="button"
                        id="evaluate-exam"
                        class="button button-primary exam-evaluate"
                        disabled
                    >
                        Prüfung auswerten
                    </button>

                    <a class="button button-secondary" href="index.php">
                        Prüfung abbrechen
                    </a>
                </section>
            </form>

            <section id="exam-result" class="exam-result" hidden>
                <p class="question-label">Dein Ergebnis</p>
                <div class="exam-score">
                    <strong id="score-value">0</strong>
                    <span>von <?= $total ?> Punkten</span>
                </div>
                <p id="score-percent"></p>
                <p id="score-message"></p>

                <div class="exam-result-actions">
                    <a class="button button-primary" href="index.php?action=pruefung">
                        Neue Prüfung mit 100 Fragen
                    </a>
                    <a class="button button-secondary" href="index.php">
                        Zurück zu den Lernkarten
                    </a>
                </div>
            </section>
        <?php endif; ?>

        <?php require __DIR__ . '/../partials/site-footer.php'; ?>
    </div>
</main>

<?php if ($total > 0): ?>
<script src="assets/js/exam.js"></script>
<?php endif; ?>
</body>
</html>
