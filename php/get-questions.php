<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['category']) || !isset($input['difficulty'])) {
    send_json_response(['error' => 'Category and difficulty are required'], 400);
}

$categorySlug = sanitize_input($input['category']);
$difficulty = sanitize_input($input['difficulty']);

$categoryMap = [
    'general-knowledge' => 9,
    'science' => 17,
    'history' => 23,
    'sports' => 21,
    'technology' => 18
];

if (!array_key_exists($categorySlug, $categoryMap)) {
    send_json_response(['error' => 'Invalid category'], 400);
}

$categoryId = $categoryMap[$categorySlug];
$amount = 5;

$apiUrl = "https://opentdb.com/api.php?amount={$amount}&category={$categoryId}&difficulty={$difficulty}&type=multiple";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    send_json_response(['error' => 'Failed to fetch questions from external API'], 500);
}

$apiData = json_decode($response, true);

if ($apiData['response_code'] !== 0) {
    send_json_response(['error' => 'API returned no results. Try a different category/difficulty.'], 404);
}

$questions = [];
foreach ($apiData['results'] as $item) {
    $questionText = html_entity_decode($item['question'], ENT_QUOTES | ENT_HTML5);
    $correctAnswer = html_entity_decode($item['correct_answer'], ENT_QUOTES | ENT_HTML5);
    $incorrectAnswers = array_map(function ($ans) {
        return html_entity_decode($ans, ENT_QUOTES | ENT_HTML5);
    }, $item['incorrect_answers']);

    $options = $incorrectAnswers;
    $options[] = $correctAnswer;
    shuffle($options);

    $correctIndex = array_search($correctAnswer, $options);

    $questions[] = [
        'question' => $questionText,
        'options' => $options,
        'correct' => $correctIndex
    ];
}

$_SESSION['quiz_questions'] = $questions;
$_SESSION['quiz_category'] = $categorySlug;
$_SESSION['quiz_difficulty'] = $difficulty;
$_SESSION['quiz_start_time'] = time();

$timeLimits = [
    'easy' => 15,
    'medium' => 20,
    'hard' => 25
];

$limit = $timeLimits[$difficulty] ?? 15;

$questionsForClient = array_map(function ($q) use ($limit) {
    return [
        'question' => $q['question'],
        'options' => $q['options'],
        'timeLimit' => $limit
    ];
}, $questions);

send_json_response([
    'success' => true,
    'questions' => $questionsForClient,
    'totalQuestions' => count($questionsForClient)
]);
?>