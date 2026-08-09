<?php
declare(strict_types=1);

final class GlossaryRepository
{
    public function __construct(private mysqli $db) {}

    public function asMap(): array
    {
        $map = [];
        $result = $this->db->query(
            'SELECT begriff, erklaerung FROM tbl_buddhismus_glossar ORDER BY CHAR_LENGTH(begriff) DESC, begriff ASC'
        );
        while ($result && $row = $result->fetch_assoc()) {
            $map[(string)$row['begriff']] = (string)$row['erklaerung'];
        }
        return $map;
    }

    public function all(): array
    {
        $rows = [];
        $result = $this->db->query(
            'SELECT id, begriff, erklaerung FROM tbl_buddhismus_glossar ORDER BY begriff ASC'
        );
        while ($result && $row = $result->fetch_assoc()) $rows[] = $row;
        return $rows;
    }

    public function add(string $term, string $explanation): bool
    {
        $stmt = $this->db->prepare('INSERT INTO tbl_buddhismus_glossar (begriff, erklaerung) VALUES (?, ?)');
        if (!$stmt) return false;
        $stmt->bind_param('ss', $term, $explanation);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function update(int $id, string $term, string $explanation): bool
    {
        $stmt = $this->db->prepare('UPDATE tbl_buddhismus_glossar SET begriff = ?, erklaerung = ? WHERE id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('ssi', $term, $explanation, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tbl_buddhismus_glossar WHERE id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
