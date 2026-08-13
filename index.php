<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap/init.php';

// Wenn PHP den gesamten POST-Body wegen post_max_size verwirft, sind sowohl
// $_POST als auch $_FILES leer. Ohne diese Prüfung würde einfach die Startseite
// erscheinen und der Benutzer sähe keine Fehlermeldung.
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && empty($_POST)
    && empty($_FILES)
    && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0
) {
    $uploadMax = (string)ini_get('upload_max_filesize');
    $postMax = (string)ini_get('post_max_size');
    $flash->set(
        'error',
        'Der Server hat den Upload bereits vor der Verarbeitung abgelehnt. '
        . 'PHP-Limits: upload_max_filesize=' . $uploadMax
        . ', post_max_size=' . $postMax . '. Bitte diese Werte beim Hosting erhöhen.'
    );
    header('Location: index.php?action=admin#dokument-upload');
    exit;
}

$formAction = (string)($_POST['form_action'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $formAction !== '') {
    $postRoutes = [
        'login' => 'login.php',
        'change_password' => 'change-password.php',
        'add_card' => 'card-add.php',
        'update_card' => 'card-update.php',
        'delete_card' => 'card-delete.php',
        'add_glossary' => 'glossary-add.php',
        'update_glossary' => 'glossary-update.php',
        'delete_glossary' => 'glossary-delete.php',
        'upload_document' => 'document-upload.php',
    ];
    if (isset($postRoutes[$formAction])) require __DIR__ . '/actions/' . $postRoutes[$formAction];
}

$action = (string)($_GET['action'] ?? '');
$routes = [
    '' => 'home.php',
    'admin' => 'admin.php',
    'glossar' => 'glossary.php',
    'glossar-pdf' => 'glossary-pdf.php',
    'pdf' => 'pdf.php',
    'pruefung' => 'exam.php',
];
if ($action === 'logout') require __DIR__ . '/actions/logout.php';
$page = $routes[$action] ?? 'home.php';
require __DIR__ . '/pages/' . $page;
