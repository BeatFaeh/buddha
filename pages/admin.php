<?php
declare(strict_types=1);

if (!$auth->isAdmin()) {
    require __DIR__ . '/admin-login.php';
    return;
}

$flashMessage = $flash->take();
$cards = $cardRepository->all();
$glossaryRows = $glossaryRepository->all();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration – Buddhistische Lernkarten</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<main class="admin-shell">
    <header class="topbar">
        <h1>Administration</h1>
        <nav class="nav-actions">
            <a class="button neutral" href="#neue-lernkarte">Neue Lernkarte</a>
            <a class="button neutral" href="#neuer-begriff">Neuer Glossarbegriff</a>
            <a class="button neutral" href="#passwort">Passwort ändern</a>
            <a class="button secondary" href="index.php">Öffentliche Ansicht</a>
            <a class="button danger" href="index.php?action=logout">Abmelden</a>
        </nav>
    </header>

    <?php if ($flashMessage): ?>
        <div class="message <?= Html::e((string) $flashMessage['type']) ?>">
            <?= Html::e((string) $flashMessage['message']) ?>
        </div>
    <?php endif; ?>

    <section class="dashboard-grid">
        <article class="panel" id="neue-lernkarte">
            <h2>Neue Frage und Antwort</h2>
            <p class="muted">Jede Lernkarte besteht aus genau einer Frage und der zugehörigen richtigen Antwort. Multiple Choice wird beim Lernen automatisch erzeugt.</p>
            <form method="post" action="index.php">
                <input type="hidden" name="csrf_token" value="<?= Html::e($csrf->token()) ?>">
                <input type="hidden" name="form_action" value="add_card">
                <label>Frage</label>
                <textarea name="frage" required></textarea>
                <label>Antwort</label>
                <textarea name="antwort" required></textarea>
                <button type="submit">Lernkarte speichern</button>
            </form>
        </article>

        <article class="panel glossary-panel" id="neuer-begriff">
            <h2>Glossar erweitern</h2>
            <form method="post" action="index.php">
                <input type="hidden" name="csrf_token" value="<?= Html::e($csrf->token()) ?>">
                <input type="hidden" name="form_action" value="add_glossary">
                <label>Begriff</label>
                <input type="text" name="begriff" maxlength="120" required>
                <label>Erklärung</label>
                <textarea name="erklaerung" required></textarea>
                <button type="submit">Glossarbegriff speichern</button>
            </form>
        </article>
    </section>

    <section class="panel list-panel" id="passwort">
        <h2>Administrationspasswort ändern</h2>
        <form method="post" action="index.php">
            <input type="hidden" name="csrf_token" value="<?= Html::e($csrf->token()) ?>">
            <input type="hidden" name="form_action" value="change_password">
            <label>Bisheriges Passwort</label>
            <input type="password" name="current_password" required>
            <label>Neues Passwort</label>
            <input type="password" name="new_password" required minlength="12">
            <label>Neues Passwort wiederholen</label>
            <input type="password" name="new_password_repeat" required minlength="12">
            <button type="submit">Passwort sicher ändern</button>
        </form>
    </section>

    <section class="panel list-panel" id="lernkarten">
        <h2>Lernkarten bearbeiten</h2>
        <p class="muted"><?= count($cards) ?> Lernkarten vorhanden.</p>

        <?php foreach ($cards as $card): ?>
            <details class="entry">
                <summary>#<?= (int) $card['id'] ?> · <?= Html::e($card['frage']) ?></summary>
                <form method="post" action="index.php">
                    <input type="hidden" name="csrf_token" value="<?= Html::e($csrf->token()) ?>">
                    <input type="hidden" name="form_action" value="update_card">
                    <input type="hidden" name="id" value="<?= (int) $card['id'] ?>">
                    <label>Frage</label>
                    <textarea name="frage" required><?= Html::e($card['frage']) ?></textarea>
                    <label>Antwort</label>
                    <textarea name="antwort" required><?= Html::e($card['antwort']) ?></textarea>
                    <button type="submit">Änderungen speichern</button>
                </form>

                <form method="post" action="index.php" class="delete-form">
                    <input type="hidden" name="csrf_token" value="<?= Html::e($csrf->token()) ?>">
                    <input type="hidden" name="form_action" value="delete_card">
                    <input type="hidden" name="id" value="<?= (int) $card['id'] ?>">
                    <button class="danger" type="submit">Lernkarte löschen</button>
                </form>
            </details>
        <?php endforeach; ?>
    </section>

    <section class="panel glossary-panel list-panel" id="glossar">
        <h2>Glossar bearbeiten</h2>
        <p class="muted"><?= count($glossaryRows) ?> Glossarbegriffe vorhanden.</p>

        <?php foreach ($glossaryRows as $row): ?>
            <details class="entry">
                <summary><?= Html::e($row['begriff']) ?></summary>
                <form method="post" action="index.php">
                    <input type="hidden" name="csrf_token" value="<?= Html::e($csrf->token()) ?>">
                    <input type="hidden" name="form_action" value="update_glossary">
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <label>Begriff</label>
                    <input type="text" name="begriff" value="<?= Html::e($row['begriff']) ?>" required>
                    <label>Erklärung</label>
                    <textarea name="erklaerung" required><?= Html::e($row['erklaerung']) ?></textarea>
                    <button type="submit">Änderungen speichern</button>
                </form>

                <form method="post" action="index.php" class="delete-form">
                    <input type="hidden" name="csrf_token" value="<?= Html::e($csrf->token()) ?>">
                    <input type="hidden" name="form_action" value="delete_glossary">
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <button class="danger" type="submit">Glossarbegriff löschen</button>
                </form>
            </details>
        <?php endforeach; ?>
    </section>
</main>
</body>
</html>
