<?php
declare(strict_types=1);

final class QuizService
{
    public function __construct(private mysqli $db) {}

    public function build(array $card, ?int $modul = null): array
    {
        $correctAnswer = trim((string)$card['antwort']);
        $answers = [$correctAnswer];

        $moduleCondition = $modul !== null && $modul >= 1 && $modul <= 6
            ? ' AND modul = ?'
            : '';
        $stmt = $this->db->prepare(
            "SELECT DISTINCT antwort FROM tbl_buddhismus "
            . "WHERE antwort IS NOT NULL AND TRIM(antwort) <> '' AND TRIM(antwort) <> ?"
            . $moduleCondition
            . " ORDER BY RAND() LIMIT 3"
        );
        if ($stmt) {
            if ($moduleCondition !== '') {
                $stmt->bind_param('si', $correctAnswer, $modul);
            } else {
                $stmt->bind_param('s', $correctAnswer);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $wrong = trim((string)($row['antwort'] ?? ''));
                if ($wrong !== '' && !in_array($wrong, $answers, true)) $answers[] = $wrong;
            }
            $stmt->close();
        }

        $options = [];
        $correctKey = '';
        if (count($answers) === 4) {
            shuffle($answers);
            foreach (['A','B','C','D'] as $i => $letter) {
                $options[$letter] = $answers[$i];
                if ($answers[$i] === $correctAnswer) $correctKey = $letter;
            }
        }

        return ['options' => $options, 'correct_key' => $correctKey];
    }
}
