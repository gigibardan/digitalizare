<?php
header('Content-Type: application/json');

// 1. Preluare întrebare
$question = json_decode(file_get_contents('php://input'), true)['question'] ?? '';

if (!$question) {
    echo json_encode(['answer' => 'Întrebare invalidă.']);
    exit;
}

// 2. Preluare cheie din .env
$env_path = __DIR__ . '/config/.env';
$api_key = trim(file_get_contents($env_path));

if (!$api_key) {
    echo json_encode(['answer' => 'Cheia API este goală sau lipsă.']);
    exit;
}

// 3. Pregătire request
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

// 4. Executare cu cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5. Procesare răspuns
$result = json_decode($response, true);

if ($http_code !== 200 || !$result || !isset($result['choices'][0]['message']['content'])) {
    echo json_encode(['answer' => 'Eroare OpenRouter (' . $http_code . '): ' . ($result['error']['message'] ?? $response)]);
    exit;
}

echo json_encode(['answer' => $result['choices'][0]['message']['content']]);
