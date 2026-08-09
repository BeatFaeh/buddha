<?php
declare(strict_types=1);
$csrf->verify();
$password = (string)($_POST['password'] ?? '');
$stored = $settingsRepository->get('admin_password_hash', (string)$appConfig['admin_password_hash']);
if (password_verify($password, $stored)) {
    $auth->login();
    $flash->set('success', 'Anmeldung erfolgreich.');
} else {
    $flash->set('error', 'Das Passwort ist nicht korrekt.');
}
header('Location: index.php?action=admin');
exit;
