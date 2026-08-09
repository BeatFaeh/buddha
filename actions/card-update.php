<?php
declare(strict_types=1);

$auth->requireAdmin();
$csrf->verify();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$frage = trim((string) ($_POST['frage'] ?? ''));
$antwort = trim((string) ($_POST['antwort'] ?? ''));

if (!$id || $frage === '' || $antwort === '') {
    $flash->set('error', 'Die Lernkarte enthält ungültige Angaben.');
} elseif ($cardRepository->update((int) $id, $frage, $antwort)) {
    $flash->set('success', 'Die Lernkarte wurde aktualisiert.');
} else {
    $flash->set('error', 'Die Lernkarte konnte nicht aktualisiert werden.');
}

header('Location: index.php?action=admin#lernkarten');
exit;
