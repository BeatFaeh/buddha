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
    public function random(): ?array
    {
        $result = $this->db->query(
            "SELECT id, frage, antwort
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
    public function findById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id, frage, antwort
             FROM tbl_buddhismus
             WHERE id = ?
               AND frage IS NOT NULL
               AND TRIM(frage) <> ''
               AND antwort IS NOT NULL
               AND TRIM(antwort) <> ''
             LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param(
            'i',
            $id
        );

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
    public function findByTerm(string $term): ?array
    {
        $term = trim($term);

        if ($term === '') {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id, frage, antwort
             FROM tbl_buddhismus
             WHERE (
                    frage LIKE CONCAT('%', ?, '%')
                    OR
                    antwort LIKE CONCAT('%', ?, '%')
                   )
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

        $stmt->bind_param(
            'ss',
            $term,
            $term
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $stmt->close();

        return $row ?: null;
    }


    /**
     * Alle Lernkarten absteigend nach ID.
     */
    public function all(): array
    {
        $rows = [];

        $result = $this->db->query(
            'SELECT id, frage, antwort
             FROM tbl_buddhismus
             ORDER BY id DESC'
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
    public function count(): int
    {
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
     * Neue Lernkarte anlegen.
     */
    public function add(
        string $frage,
        string $antwort
    ): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO tbl_buddhismus
             (frage, antwort)
             VALUES (?, ?)'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            'ss',
            $frage,
            $antwort
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
        string $antwort
    ): bool {
        $stmt = $this->db->prepare(
            'UPDATE tbl_buddhismus
             SET frage = ?,
                 antwort = ?
             WHERE id = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            'ssi',
            $frage,
            $antwort,
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