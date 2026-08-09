<?php
declare(strict_types=1);

return [
    'host' => getenv('BUDDHISMUS_DB_HOST') ?: 'localhost',
    'username' => getenv('BUDDHISMUS_DB_USERNAME') ?: 'absolutions_usr',
    'password' => getenv('BUDDHISMUS_DB_PASSWORD') ?: '4m29$2Dhp',
    'database' => getenv('BUDDHISMUS_DB_NAME') ?: 'absolutions_db',
];
