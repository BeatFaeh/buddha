<?php
declare(strict_types=1);

$auth->requireAdmin();
$csrf->verify();

$frage = trim((string) ($_POST['frage'] ?? ''));
$antwort = trim((string) ($_POST['antwort'] ?? ''));

if ($frage === '' || $antwort === '') {
    $flash->set('error', 'Frage und Antwort müssen ausgefüllt sein.');
} elseif ($cardRepository->add($frage, $antwort)) {
    $flash->set('success', 'Die neue Lernkarte wurde gespeichert.');
} else {
    $flash->set('error', 'Die Lernkarte konnte nicht gespeichert werden.');
}

header('Location: index.php?action=admin#lernkarten');
exit;
