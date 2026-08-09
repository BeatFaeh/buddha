<?php
declare(strict_types=1);

$db->query(
    "CREATE TABLE IF NOT EXISTS tbl_buddhismus (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        frage TEXT NOT NULL,
        antwort TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$db->query(
    "CREATE TABLE IF NOT EXISTS tbl_buddhismus_glossar (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        begriff VARCHAR(120) NOT NULL,
        erklaerung TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_buddhismus_glossar_begriff (begriff)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$db->query(
    "CREATE TABLE IF NOT EXISTS tbl_buddhismus_einstellungen (
        einstellungsname VARCHAR(100) NOT NULL,
        einstellungswert TEXT NOT NULL,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (einstellungsname)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);
