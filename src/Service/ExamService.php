<?php
declare(strict_types=1);

final class ExamService
{
    public function __construct(
        private CardRepository $cards,
        private QuizService $quiz
    ) {}

    public function build(int $requestedQuestions = 100): array
    {
        $cards = $this->cards->randomMany($requestedQuestions);
        $questions = [];

        foreach ($cards as $card) {
            $quiz = $this->quiz->build($card);

            if (count($quiz['options']) !== 4 || $quiz['correct_key'] === '') {
                continue;
            }

            $questions[] = [
                'id' => (int)$card['id'],
                'frage' => (string)$card['frage'],
                'options' => $quiz['options'],
                'correct_key' => $quiz['correct_key'],
            ];
        }

        return [
            'requested' => $requestedQuestions,
            'questions' => $questions,
            'count' => count($questions),
        ];
    }
}
