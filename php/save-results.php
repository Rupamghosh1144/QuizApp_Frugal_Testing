<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Method not allowed'], 405);
}

if (!isset($_SESSION['quiz_results'])) {
    send_json_response(['error' => 'No results to save'], 400);
}

$resultId = 'quiz_' . date('Ymd_His') . '_' . uniqid();
$filename = RESULTS_DIR . $resultId . '.json';

$dataToSave = [
    'resultId' => $resultId,
    'results' => $_SESSION['quiz_results'],
    'savedAt' => date('Y-m-d H:i:s')
];

$jsonData = json_encode($dataToSave, JSON_PRETTY_PRINT);
$saved = file_put_contents($filename, $jsonData);

if ($saved === false) {
    send_json_response(['error' => 'Failed to save results'], 500);
}

send_json_response([
    'success' => true,
    'resultId' => $resultId,
    'message' => 'Results saved successfully'
]);
?>