<?php
declare(strict_types=1);

final class CardRepository
{
    public function __construct(private mysqli $db) {}

    public function random(): ?array
    {
        $result = $this->db->query(
            "SELECT id, frage, antwort FROM tbl_buddhismus "
            . "WHERE frage IS NOT NULL AND TRIM(frage) <> '' "
            . "AND antwort IS NOT NULL AND TRIM(antwort) <> '' ORDER BY RAND() LIMIT 1"
        );

        return $result ? ($result->fetch_assoc() ?: null) : null;
    }


    public function randomMany(int $limit): array
    {
        $limit = max(1, min(1000, $limit));
        $rows = [];

        $result = $this->db->query(
            "SELECT id, frage, antwort FROM tbl_buddhismus "
            . "WHERE frage IS NOT NULL AND TRIM(frage) <> '' "
            . "AND antwort IS NOT NULL AND TRIM(antwort) <> '' "
            . "ORDER BY RAND() LIMIT " . $limit
        );

        while ($result && $row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function all(): array
    {
        $rows = [];
        $result = $this->db->query(
            'SELECT id, frage, antwort FROM tbl_buddhismus ORDER BY id DESC'
        );

        while ($result && $row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function allAscending(): array
    {
        $rows = [];
        $result = $this->db->query(
            'SELECT id, frage, antwort FROM tbl_buddhismus ORDER BY id ASC'
        );

        while ($result && $row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function search(string $search): array
    {
        $rows = [];
        $stmt = $this->db->prepare(
            "SELECT id, frage, antwort FROM tbl_buddhismus "
            . "WHERE frage LIKE CONCAT('%', ?, '%') OR antwort LIKE CONCAT('%', ?, '%') ORDER BY id ASC"
        );

        if (!$stmt) {
            return $rows;
        }

        $stmt->bind_param('ss', $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $stmt->close();
        return $rows;
    }

    public function count(): int
    {
        $result = $this->db->query(
            "SELECT COUNT(*) AS anzahl FROM tbl_buddhismus "
            . "WHERE frage IS NOT NULL AND TRIM(frage) <> '' "
            . "AND antwort IS NOT NULL AND TRIM(antwort) <> ''"
        );

        $row = $result ? $result->fetch_assoc() : null;
        return (int) ($row['anzahl'] ?? 0);
    }

    public function add(string $frage, string $antwort): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tbl_buddhismus (frage, antwort) VALUES (?, ?)'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ss', $frage, $antwort);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function update(int $id, string $frage, string $antwort): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE tbl_buddhismus SET frage = ?, antwort = ? WHERE id = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ssi', $frage, $antwort, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tbl_buddhismus WHERE id = ?');

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
