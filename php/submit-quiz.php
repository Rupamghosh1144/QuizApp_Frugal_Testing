<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['answers']) || !isset($input['timeTaken'])) {
    send_json_response(['error' => 'Answers and time data are required'], 400);
}

if (!isset($_SESSION['quiz_questions'])) {
    send_json_response(['error' => 'No active quiz session'], 400);
}

$userAnswers = $input['answers'];
$questions = $_SESSION['quiz_questions'];
$timeTaken = $input['timeTaken'];

$totalQuestions = count($questions);
$correctAnswers = 0;
$incorrectAnswers = 0;
$detailedResults = [];

foreach ($questions as $index => $question) {
    $userAnswer = isset($userAnswers[$index]) ? (int) $userAnswers[$index] : -1;
    $correctAnswer = (int) $question['correct'];
    $isCorrect = ($userAnswer === $correctAnswer);

    if ($isCorrect) {
        $correctAnswers++;
    } else {
        $incorrectAnswers++;
    }

    $detailedResults[] = [
        'question' => $question['question'],
        'options' => $question['options'],
        'userAnswer' => $userAnswer,
        'correctAnswer' => $correctAnswer,
        'isCorrect' => $isCorrect,
        'timeTaken' => isset($timeTaken[$index]) ? $timeTaken[$index] : 0
    ];
}

$percentage = ($correctAnswers / $totalQuestions) * 100;

$totalTime = array_sum($timeTaken);

$_SESSION['quiz_results'] = [
    'category' => $_SESSION['quiz_category'],
    'difficulty' => $_SESSION['quiz_difficulty'],
    'totalQuestions' => $totalQuestions,
    'correctAnswers' => $correctAnswers,
    'incorrectAnswers' => $incorrectAnswers,
    'percentage' => round($percentage, 2),
    'detailedResults' => $detailedResults,
    'totalTime' => $totalTime,
    'completedAt' => date('Y-m-d H:i:s')
];

send_json_response([
    'success' => true,
    'score' => $correctAnswers,
    'totalQuestions' => $totalQuestions,
    'percentage' => round($percentage, 2),
    'correctAnswers' => $correctAnswers,
    'incorrectAnswers' => $incorrectAnswers,
    'totalTime' => $totalTime
]);
?>