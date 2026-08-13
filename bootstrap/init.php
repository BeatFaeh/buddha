<?php
declare(strict_types=1);

$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';

session_name((string)$appConfig['session_name']);
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Strict',
    'path' => '/',
]);
session_start();

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Security/Auth.php';
require_once __DIR__ . '/../src/Security/Csrf.php';
require_once __DIR__ . '/../src/Support/Flash.php';
require_once __DIR__ . '/../src/Support/Html.php';
require_once __DIR__ . '/../src/Repository/SettingsRepository.php';
require_once __DIR__ . '/../src/Repository/CardRepository.php';
require_once __DIR__ . '/../src/Repository/GlossaryRepository.php';
require_once __DIR__ . '/../src/Service/QuizService.php';
require_once __DIR__ . '/../src/Service/ExamService.php';
require_once __DIR__ . '/../src/Service/GlossaryFormatter.php';

$database = new Database($dbConfig);
$db = $database->connection();
$auth = new Auth();
$csrf = new Csrf();
$flash = new Flash();
$settingsRepository = new SettingsRepository($db);
$cardRepository = new CardRepository($db);
$glossaryRepository = new GlossaryRepository($db);
$quizService = new QuizService($db);
$examService = new ExamService($cardRepository, $quizService);
$glossaryFormatter = new GlossaryFormatter();

require __DIR__ . '/../database/schema.php';
/* require __DIR__ . '/../database/seed.php'; */
