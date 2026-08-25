<?php

declare(strict_types=1);

final class CardRepository
{
    public function __construct(
        private mysqli $db
    ) {
    }


    /**
     * Eine zufällige Lernkarte laden.
     */
    public function random(?int $modul = null): ?array
    {
        if ($modul !== null && $modul >= 1 && $modul <= 6) {
            $stmt = $this->db->prepare(
                "SELECT id, frage, antwort, modul
                 FROM tbl_buddhismus
                 WHERE modul = ?
                   AND frage IS NOT NULL
                   AND TRIM(frage) <> ''
                   AND antwort IS NOT NULL
                   AND TRIM(antwort) <> ''
                 ORDER BY RAND()
                 LIMIT 1"
            );

            if (!$stmt) {
                return null;
            }

            $stmt->bind_param('i', $modul);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return $row ?: null;
        }

        $result = $this->db->query(
            "SELECT id, frage, antwort, modul
             FROM tbl_buddhismus
             WHERE frage IS NOT NULL
               AND TRIM(frage) <> ''
               AND antwort IS NOT NULL
               AND TRIM(antwort) <> ''
             ORDER BY RAND()
             LIMIT 1"
        );

        return $result
            ? ($result->fetch_assoc() ?: null)
            : null;
    }


    /**
     * Mehrere zufällige Lernkarten laden.
     */
    public function randomMany(int $limit): array
    {
        $limit = max(
            1,
            min(1000, $limit)
        );

        $rows = [];

        $result = $this->db->query(
            "SELECT id, frage, antwort
             FROM tbl_buddhismus
             WHERE frage IS NOT NULL
               AND TRIM(frage) <> ''
               AND antwort IS NOT NULL
               AND TRIM(antwort) <> ''
             ORDER BY RAND()
             LIMIT " . $limit
        );

        while (
            $result
            && $row = $result->fetch_assoc()
        ) {
            $rows[] = $row;
        }

        return $rows;
    }


    /**
     * Eine Lernkarte anhand ihrer ID laden.
     */
    public function findById(int $id, ?int $modul = null): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $moduleCondition = $modul !== null && $modul >= 1 && $modul <= 6
            ? ' AND modul = ?'
            : '';

        $stmt = $this->db->prepare(
            "SELECT id, frage, antwort, modul
             FROM tbl_buddhismus
             WHERE id = ?
               {$moduleCondition}
               AND frage IS NOT NULL
               AND TRIM(frage) <> ''
               AND antwort IS NOT NULL
               AND TRIM(antwort) <> ''
             LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        if ($moduleCondition !== '') {
            $stmt->bind_param('ii', $id, $modul);
        } else {
            $stmt->bind_param('i', $id);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $stmt->close();

        return $row ?: null;
    }


    /**
     * Eine Lernkarte anhand eines Begriffs suchen.
     *
     * Gesucht wird in Frage und Antwort.
     *
     * Falls mehrere Karten passen,
     * wird die Karte mit der kleinsten ID geladen.
     */
    public function findByTerm(string $term, ?int $modul = null): ?array
    {
        $term = trim($term);

        if ($term === '') {
            return null;
        }

        $moduleCondition = $modul !== null && $modul >= 1 && $modul <= 6
            ? ' AND modul = ?'
            : '';

        $stmt = $this->db->prepare(
            "SELECT id, frage, antwort, modul
             FROM tbl_buddhismus
             WHERE (
                    frage LIKE CONCAT('%', ?, '%')
                    OR
                    antwort LIKE CONCAT('%', ?, '%')
                   )
               {$moduleCondition}
               AND frage IS NOT NULL
               AND TRIM(frage) <> ''
               AND antwort IS NOT NULL
               AND TRIM(antwort) <> ''
             ORDER BY id ASC
             LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        if ($moduleCondition !== '') {
            $stmt->bind_param('ssi', $term, $term, $modul);
        } else {
            $stmt->bind_param('ss', $term, $term);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $stmt->close();

        return $row ?: null;
    }


    /**
     * Alle Lernkarten absteigend nach ID.
     */
    public function all(?int $modul = null): array
    {
        $rows = [];

        if ($modul !== null && $modul >= 1 && $modul <= 6) {
            $stmt = $this->db->prepare(
                'SELECT id, frage, antwort, modul
                 FROM tbl_buddhismus
                 WHERE modul = ?
                 ORDER BY id DESC'
            );

            if (!$stmt) {
                return $rows;
            }

            $stmt->bind_param('i', $modul);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $stmt = null;
            $result = $this->db->query(
                'SELECT id, frage, antwort, modul
                 FROM tbl_buddhismus
                 ORDER BY id DESC'
            );
        }

        while (
            $result
            && $row = $result->fetch_assoc()
        ) {
            $rows[] = $row;
        }

        if ($stmt !== null) {
            $stmt->close();
        }

        return $rows;
    }


    /**
     * Alle Lernkarten aufsteigend nach ID.
     */
    public function allAscending(): array
    {
        $rows = [];

        $result = $this->db->query(
            'SELECT id, frage, antwort
             FROM tbl_buddhismus
             ORDER BY id ASC'
        );

        while (
            $result
            && $row = $result->fetch_assoc()
        ) {
            $rows[] = $row;
        }

        return $rows;
    }


    /**
     * Mehrere Karten anhand eines Suchbegriffs suchen.
     *
     * Diese bestehende Methode bleibt für andere
     * Bereiche der Anwendung erhalten.
     */
    public function search(string $search): array
    {
        $rows = [];

        $stmt = $this->db->prepare(
            "SELECT id, frage, antwort
             FROM tbl_buddhismus
             WHERE frage LIKE CONCAT('%', ?, '%')
                OR antwort LIKE CONCAT('%', ?, '%')
             ORDER BY id ASC"
        );

        if (!$stmt) {
            return $rows;
        }

        $stmt->bind_param(
            'ss',
            $search,
            $search
        );

        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $stmt->close();

        return $rows;
    }


    /**
     * Anzahl aller vollständigen Lernkarten.
     */
    public function count(?int $modul = null): int
    {
        if ($modul !== null && $modul >= 1 && $modul <= 6) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) AS anzahl
                 FROM tbl_buddhismus
                 WHERE modul = ?
                   AND frage IS NOT NULL
                   AND TRIM(frage) <> ''
                   AND antwort IS NOT NULL
                   AND TRIM(antwort) <> ''"
            );

            if (!$stmt) {
                return 0;
            }

            $stmt->bind_param('i', $modul);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return (int) ($row['anzahl'] ?? 0);
        }

        $result = $this->db->query(
            "SELECT COUNT(*) AS anzahl
             FROM tbl_buddhismus
             WHERE frage IS NOT NULL
               AND TRIM(frage) <> ''
               AND antwort IS NOT NULL
               AND TRIM(antwort) <> ''"
        );

        $row = $result
            ? $result->fetch_assoc()
            : null;

        return (int) ($row['anzahl'] ?? 0);
    }


    /**
     * Anzahl vollständiger Lernkarten je Modul (1 bis 6).
     */
    public function countByModule(): array
    {
        $counts = array_fill(1, 6, 0);

        $result = $this->db->query(
            "SELECT modul, COUNT(*) AS anzahl
             FROM tbl_buddhismus
             WHERE modul BETWEEN 1 AND 6
               AND frage IS NOT NULL
               AND TRIM(frage) <> ''
               AND antwort IS NOT NULL
               AND TRIM(antwort) <> ''
             GROUP BY modul
             ORDER BY modul ASC"
        );

        while ($result && $row = $result->fetch_assoc()) {
            $counts[(int) $row['modul']] = (int) $row['anzahl'];
        }

        return $counts;
    }


    /**
     * Neue Lernkarte anlegen.
     */
    public function add(
        string $frage,
        string $antwort,
        int $modul
    ): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO tbl_buddhismus
             (frage, antwort, modul)
             VALUES (?, ?, ?)'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            'ssi',
            $frage,
            $antwort,
            $modul
        );

        $ok = $stmt->execute();

        $stmt->close();

        return $ok;
    }


    /**
     * Bestehende Lernkarte aktualisieren.
     */
    public function update(
        int $id,
        string $frage,
        string $antwort,
        int $modul
    ): bool {
        $stmt = $this->db->prepare(
            'UPDATE tbl_buddhismus
             SET frage = ?,
                 antwort = ?,
                 modul = ?
             WHERE id = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            'ssii',
            $frage,
            $antwort,
            $modul,
            $id
        );

        $ok = $stmt->execute();

        $stmt->close();

        return $ok;
    }


    /**
     * Lernkarte löschen.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM tbl_buddhismus
             WHERE id = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            'i',
            $id
        );

        $ok = $stmt->execute();

        $stmt->close();

        return $ok;
    }
}
