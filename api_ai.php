<?php
header('Content-Type: application/json');

$question = json_decode(file_get_contents('php://input'), true)['question'] ?? '';

if (!$question) {
    echo json_encode(['answer' => 'Întrebare invalidă.']);
    exit;
}

// Încarcă cheia
include __DIR__ . '/includes/secret_key.php';
$api_key = $API_KEY;

$url = 'https://openrouter.ai/api/v1/chat/completions';

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
];

$data = [
    'model' => 'mistralai/mistral-7b-instruct',
    'messages' => [
        ['role' => 'system', 'content' => 'Ești un asistent pentru profesori, specializat în educație și digitalizare.'],
        ['role' => 'user', 'content' => $question]
    ]
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

$response = file_get_contents($url, false, stream_context_create($options));
$result = json_decode($response, true);

// Debug:
if (!$result || !isset($result['choices'][0]['message']['content'])) {
    echo json_encode(['answer' => 'Eroare brută OpenRouter: ' . $response]);
    exit;
}

echo json_encode(['answer' => $result['choices'][0]['message']['content']]);
