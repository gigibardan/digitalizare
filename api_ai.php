<?php
// api_ai.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents('php://input'), true);
$question = trim($input['question'] ?? '');

if (!$question) {
    echo json_encode(['answer' => 'Întrebare invalidă.']);
    exit;
}

// Încarcă cheia API din fișierul securizat
$api_key = null;
$key_file = __DIR__ . '/includes/secret_key.php';

if (file_exists($key_file)) {
    define('ALLOW_INCLUDE', true);
    $api_key = include $key_file;
} else {
    echo json_encode([
        'answer' => 'Fișierul cu cheia API nu există.',
        'debug' => 'Caută: ' . $key_file
    ]);
    exit;
}

if (!$api_key || strpos($api_key, 'sk-your-actual') !== false) {
    echo json_encode(['answer' => 'Cheia API nu a fost configurată corect.']);
    exit;
}

$url = 'https://openrouter.ai/api/v1/chat/completions';

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
];

$data = [
    'model' => 'google/gemini-2.0-flash-exp:free',
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

if ($response === false) {
    echo json_encode(['answer' => 'Eroare la conectarea cu OpenRouter API.']);
    exit;
}

$result = json_decode($response, true);

if (!$result || !isset($result['choices'][0]['message']['content'])) {
    echo json_encode([
        'answer' => 'Eroare la procesarea răspunsului.',
        'debug' => $response
    ]);
    exit;
}

echo json_encode(['answer' => $result['choices'][0]['message']['content']]);
?>