<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap/init.php';

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
    ];
    if (isset($postRoutes[$formAction])) require __DIR__ . '/actions/' . $postRoutes[$formAction];
}

$action = (string)($_GET['action'] ?? '');
$routes = [
    '' => 'home.php',
    'admin' => 'admin.php',
    'glossar' => 'glossary.php',
    'pdf' => 'pdf.php',
    'pruefung' => 'exam.php',
];
if ($action === 'logout') require __DIR__ . '/actions/logout.php';
$page = $routes[$action] ?? 'home.php';
require __DIR__ . '/pages/' . $page;
