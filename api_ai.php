<?php
// api_ai.php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$question = trim($input['question'] ?? '');

if (!$question) {
    echo json_encode(['answer' => 'Întrebare invalidă.']);
    exit;
}

$api_key = include __DIR__ . '/includes/secret_key.php';

if (!$api_key) {
    echo json_encode(['answer' => 'Cheia API nu a fost încărcată.']);
    exit;
}

$url = 'https://openrouter.ai/api/v1/chat/completions';

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
];

$data = [
    'model' => 'mistralai/mistral-7b-instruct',
    'messages' => [
        ['role' => 'system', 'content' => 'Ești un asistent pentru profesori, specializat în educație și digitalizare. Răspunde clar și pe înțelesul cadrelor didactice.'],
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

// DEBUG:
if (!$result || !isset($result['choices'][0]['message']['content'])) {
    echo json_encode(['answer' => 'Eroare brută OpenRouter: ' . $response]);
    exit;
}

echo json_encode(['answer' => $result['choices'][0]['message']['content']]);

