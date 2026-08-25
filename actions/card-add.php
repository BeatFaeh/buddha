<?php
declare(strict_types=1);

$auth->requireAdmin();
$csrf->verify();

$frage = trim((string) ($_POST['frage'] ?? ''));
$antwort = trim((string) ($_POST['antwort'] ?? ''));
$modul = filter_input(INPUT_POST, 'modul', FILTER_VALIDATE_INT);

if ($modul === false || $modul === null || $modul < 1 || $modul > 6) {
    $flash->set('error', 'Bitte ein gültiges Modul von 1 bis 6 auswählen.');
} elseif ($frage === '' || $antwort === '') {
    $flash->set('error', 'Modul, Frage und Antwort müssen ausgefüllt sein.');
} elseif ($cardRepository->add($frage, $antwort, $modul)) {
    $flash->set('success', 'Die neue Lernkarte wurde gespeichert.');
} else {
    $flash->set('error', 'Die Lernkarte konnte nicht gespeichert werden.');
}

header('Location: index.php?action=admin#lernkarten');
exit;
