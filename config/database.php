<?php
declare(strict_types=1);

return [
    'host' => getenv('BUDDHISMUS_DB_HOST') ?: 'localhost',
    'username' => getenv('BUDDHISMUS_DB_USERNAME') ?: 'buddha_usr',
    'password' => getenv('BUDDHISMUS_DB_PASSWORD') ?: 'YEh_66Sb0smi*lxe',
    'database' => getenv('BUDDHISMUS_DB_NAME') ?: 'buddha_db',
];
