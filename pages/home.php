<?php

declare(strict_types=1);


/* =========================================================
   LERNMODUS
   ========================================================= */

$mode = (string) ($_GET['modus'] ?? 'text');

if (!in_array($mode, ['text', 'mc'], true)) {
    $mode = 'text';
}

$selectedModule = filter_input(INPUT_GET, 'modul', FILTER_VALIDATE_INT);
if ($selectedModule === false || $selectedModule === null || $selectedModule < 1 || $selectedModule > 6) {
    $selectedModule = null;
}

$moduleQuery = $selectedModule !== null
    ? '&modul=' . $selectedModule
    : '';


/* =========================================================
   GEZIELTE KARTENAUSWAHL
   ========================================================= */

$search = trim(
    (string) ($_GET['suche'] ?? '')
);

$searchMessage = '';


/*
 * Wenn ein Suchwert eingegeben wurde,
 * wird gezielt gesucht.
 *
 * Ohne Suchwert bleibt das bisherige Verhalten:
 * eine zufällige Lernkarte wird geladen.
 */
if ($search !== '') {

    /*
     * Reine Zahl:
     * exakte Suche nach Lernkarten-ID.
     */
    if (ctype_digit($search)) {

        $card = $cardRepository->findById(
            (int) $search,
            $selectedModule
        );

        if (!$card) {
            $searchMessage =
                'Keine Lernkarte mit der ID '
                . $search
                . ($selectedModule !== null ? ' in Modul ' . $selectedModule : '')
                . ' gefunden.';
        }

    } else {

        /*
         * Text:
         * Suche nach Begriff in Frage und Antwort.
         */
        $card = $cardRepository->findByTerm(
            $search,
            $selectedModule
        );

        if (!$card) {
            $searchMessage =
                'Keine Lernkarte zum Begriff „'
                . $search
                . '“'
                . ($selectedModule !== null ? ' in Modul ' . $selectedModule : '')
                . ' gefunden.';
        }
    }

} else {

    /*
     * Bestehende Zufallsfunktion.
     */
    $card = $cardRepository->random($selectedModule);
}


$count = $cardRepository->count($selectedModule);
$moduleCounts = $cardRepository->countByModule();


/* =========================================================
   MULTIPLE CHOICE
   ========================================================= */

$quiz = [
    'options'     => [],
    'correct_key' => '',
];

if ($mode === 'mc' && $card) {
    $quiz = $quizService->build($card, $selectedModule);
}


/* =========================================================
   GLOSSAR
   ========================================================= */

$glossary = $glossaryRepository->asMap();

?>
<!doctype html>

<html lang="de">

<head>

    <meta charset="utf-8">

    <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
    >

    <title>Buddhistische Lernkarten</title>

    <link
            rel="stylesheet"
            href="assets/css/main.css"
    >

</head>

<body>

<main class="page">

    <div class="wrapper">

        <?php require __DIR__ . '/../partials/public-hero.php'; ?>


        <!-- =====================================================
             LERNMODUS
             ===================================================== -->

        <nav
                class="mode-switch"
                aria-label="Lernmodus wählen"
        >

            <a
                    class="mode-button <?= $mode === 'text' ? 'active' : '' ?>"
                    href="index.php?modus=text<?= Html::e($moduleQuery) ?>"
            >
                📖 Lernkarte
            </a>

            <a
                    class="mode-button <?= $mode === 'mc' ? 'active' : '' ?>"
                    href="index.php?modus=mc<?= Html::e($moduleQuery) ?>"
            >
                ✓ Multiple Choice
            </a>

        </nav>


        <!-- =====================================================
             MODULFILTER UND ANZAHL LERNKARTEN
             ===================================================== -->

        <section class="module-panel" aria-labelledby="module-filter-title">

            <form class="module-filter" method="get" action="index.php">
                <input type="hidden" name="modus" value="<?= Html::e($mode) ?>">

                <label id="module-filter-title" for="module-filter-select">
                    Lernkarten nach Modul auswählen
                </label>

                <div class="module-filter-row">
                    <select id="module-filter-select" name="modul">
                        <option value="">Alle Module</option>
                        <?php for ($moduleOption = 1; $moduleOption <= 6; $moduleOption++): ?>
                            <option value="<?= $moduleOption ?>" <?= $selectedModule === $moduleOption ? 'selected' : '' ?>>
                                Modul <?= $moduleOption ?> (<?= $moduleCounts[$moduleOption] ?>)
                            </option>
                        <?php endfor; ?>
                    </select>

                    <button class="button button-primary" type="submit">
                        Modul anwenden
                    </button>
                </div>
            </form>

            <div class="module-counts" aria-label="Anzahl Lernkarten pro Modul">
                <?php for ($moduleOption = 1; $moduleOption <= 6; $moduleOption++): ?>
                    <a
                            class="module-count <?= $selectedModule === $moduleOption ? 'active' : '' ?>"
                            href="index.php?modus=<?= Html::e($mode) ?>&modul=<?= $moduleOption ?>"
                    >
                        <span>Modul <?= $moduleOption ?></span>
                        <strong><?= $moduleCounts[$moduleOption] ?></strong>
                    </a>
                <?php endfor; ?>
            </div>

        </section>


        <!-- =====================================================
             LERNKARTE GEZIELT AUSWÄHLEN
             ===================================================== -->

        <form
                class="card-search"
                method="get"
                action="index.php"
        >

            <input
                    type="hidden"
                    name="modus"
                    value="<?= Html::e($mode) ?>"
            >

            <?php if ($selectedModule !== null): ?>
                <input type="hidden" name="modul" value="<?= $selectedModule ?>">
            <?php endif; ?>

            <label
                    class="card-search-label"
                    for="card-search-input"
            >
                Lernkarte gezielt auswählen
            </label>

            <div class="card-search-row">

                <input
                        id="card-search-input"
                        class="card-search-input"
                        type="search"
                        name="suche"
                        value="<?= Html::e($search) ?>"
                        placeholder="ID oder Begriff, z. B. 53 oder Skandhas"
                        autocomplete="off"
                >

                <button
                        class="button button-primary card-search-button"
                        type="submit"
                >
                    🔎 Suchen
                </button>
<p>&nbsp;</p>
            </div>

        </form>


        <!-- =====================================================
             SUCHMELDUNG
             ===================================================== -->

        <?php if ($searchMessage !== ''): ?>

            <div
                    class="search-message"
                    role="status"
            >
                <?= Html::e($searchMessage) ?>
            </div>

        <?php endif; ?>


        <?php if (!$card): ?>

            <!-- =================================================
                 KEINE PASSENDE LERNKARTE
                 ================================================= -->

            <section class="learning-card">

                <div class="card-content">

                    <h2>
                        <?= $search !== ''
                            ? 'Keine passende Lernkarte gefunden'
                            : 'Keine Lernkarten vorhanden'
                        ?>
                    </h2>


                    <!-- =========================================
                         ZUFALLSFUNKTION BLEIBT AUCH HIER ERHALTEN
                         ========================================= -->

                    <?php if ($search !== ''): ?>

                        <div class="actions actions-single">

                            <a
                                    class="button button-primary"
                                    href="index.php?modus=<?= Html::e($mode) ?><?= Html::e($moduleQuery) ?>"
                            >
                                <?= $mode === 'mc'
                                    ? 'Neue Multiple-Choice-Frage'
                                    : 'Neue Zufallsfrage'
                                ?>
                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            </section>


        <?php else: ?>

            <!-- =================================================
                 LERNKARTE
                 ================================================= -->

            <section class="learning-card">

                <div class="card-content">


                    <!-- =========================================
                         META-INFORMATIONEN
                         ========================================= -->

                    <div class="meta">

                        <span class="badge">

                            <?= $mode === 'mc'
                                ? 'Multiple Choice'
                                : 'Karte'
                            ?>

                            #<?= (int) $card['id'] ?>

                            <?php if ((int) ($card['modul'] ?? 0) >= 1 && (int) ($card['modul'] ?? 0) <= 6): ?>
                                · Modul <?= (int) $card['modul'] ?>
                            <?php endif; ?>

                        </span>

                        <span class="counter">

                            <?= $count ?>

                            <?= $count === 1
                                ? 'Lernkarte'
                                : 'Lernkarten'
                            ?>

                            <?= $selectedModule !== null
                                ? 'in Modul ' . $selectedModule
                                : 'insgesamt'
                            ?>

                        </span>

                    </div>


                    <!-- =========================================
                         FRAGE
                         ========================================= -->

                    <p class="question-label">

                        <?= $mode === 'mc'
                            ? 'Multiple-Choice-Frage'
                            : 'Frage'
                        ?>

                    </p>

                    <h2 class="question">
                        <?= Html::e($card['frage']) ?>
                    </h2>


                    <?php if ($mode === 'mc'): ?>

                        <!-- =====================================
                             MULTIPLE CHOICE
                             ===================================== -->

                        <?php if (
                            count($quiz['options']) === 4
                            && $quiz['correct_key'] !== ''
                        ): ?>

                            <div
                                    class="mc-quiz"
                                    data-correct="<?= Html::e(
                                        $quiz['correct_key']
                                    ) ?>"
                            >

                                <?php foreach (
                                    $quiz['options']
                                    as $letter => $answer
                                ): ?>

                                    <button
                                            type="button"
                                            class="mc-option"
                                            data-option="<?= Html::e($letter) ?>"
                                    >

                                        <span class="mc-letter">
                                            <?= Html::e($letter) ?>
                                        </span>

                                        <span>
                                            <?= Html::e($answer) ?>
                                        </span>

                                    </button>

                                <?php endforeach; ?>

                                <div
                                        class="mc-feedback"
                                        aria-live="polite"
                                ></div>

                            </div>

                        <?php else: ?>

                            <div class="mc-error">

                                Für Multiple Choice werden mindestens
                                vier unterschiedliche Antworten in

                                <code>
                                    tbl_buddhismus.antwort
                                </code>

                                <?= $selectedModule !== null
                                    ? 'innerhalb von Modul ' . $selectedModule
                                    : ''
                                ?> benötigt.

                            </div>

                        <?php endif; ?>


                    <?php else: ?>

                        <!-- =====================================
                             ANTWORT
                             ===================================== -->

                        <details class="accordion">

                            <summary>
                                Antwort anzeigen
                            </summary>

                            <div class="accordion-content">

                                <?= $glossaryFormatter->format(
                                    $card['antwort'],
                                    $glossary
                                ) ?>

                            </div>

                        </details>

                    <?php endif; ?>


                    <!-- =========================================
                         AKTIONEN
                         ========================================= -->

                    <div class="actions">


                        <!-- =====================================
                             ZUFALLSFRAGE BLEIBT UNVERÄNDERT
                             ===================================== -->

                        <a
                                class="button button-primary"
                                href="index.php?modus=<?= Html::e($mode) ?><?= Html::e($moduleQuery) ?>"
                        >

                            <?= $mode === 'mc'
                                ? 'Neue Multiple-Choice-Frage'
                                : 'Neue Zufallsfrage'
                            ?>

                        </a>
                        <a class="button button-secondary" href="index.php?action=pdf" >Alle Lernkarten / PDF</a>
                        <a class="button button-secondary" href="index.php?action=glossar">Glossar</a>
                        <a class="button button-primary" href="index.php?action=pruefung">📝 Prüfung · 100 Fragen</a>
                        <a class="button button-secondary" href="literatur/">📚 Literatur</a>
                        <a class="button button-secondary" href="lernmodule/">🎓 Lernmodule</a>
                        <a class="button button-secondary" href="meditation/">🧘 Meditation</a>
                        <a class="button button-admin" href="index.php?action=admin">Administration</a>

                    </div>

                </div>

            </section>

        <?php endif; ?>


        <?php require __DIR__ . '/../partials/site-footer.php'; ?>

    </div>

</main>


<script src="assets/js/tooltips.js"></script>
<script src="assets/js/quiz.js"></script>

</body>

</html>
