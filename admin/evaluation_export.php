<?php
// admin/evaluation_export.php
session_start();

// Verifică autentificarea admin
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['user_type'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once '../includes/auth.php';
require_once '../config/database.php';

// Setează header-ele pentru descărcare CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="teste_evaluare_' . date('Y-m-d_H-i-s') . '.csv"');

// Creează output stream-ul
$output = fopen('php://output', 'w');

// Adaugă BOM pentru UTF-8 (pentru Excel)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header CSV
$headers = [
    'ID Test',
    'Nume',
    'Prenume', 
    'CNP',
    'Instituția',
    'Scor Total',
    'Procent',
    'Status',
    'Data Start',
    'Data Finalizare',
    'Durată (minute)',
    'IP Address',
    'Modul 1 - Corect',
    'Modul 2 - Corect',
    'Modul 3 - Corect',
    'Modul 4 - Corect',
    'Modul 5 - Corect',
    'Modul 6 - Corect',
    'Răspunsuri Detaliate'
];

fputcsv($output, $headers);

try {
    // Obține toate testele finalizate
    $stmt = $pdo->query("
        SELECT *, 
               TIMESTAMPDIFF(MINUTE, timp_start, timp_finalizare) as durata_minute
        FROM test_attempts 
        WHERE status IN ('promovat', 'esuat')
        ORDER BY timp_start DESC
    ");
    $tests = $stmt->fetchAll();
    
    // Obține toate întrebările pentru maparea răspunsurilor
    $stmt = $pdo->query("SELECT * FROM intrebari ORDER BY modul, id");
    $questions = $stmt->fetchAll();
    $questionsById = [];
    $questionsByModule = [];
    
    foreach ($questions as $question) {
        $questionsById[$question['id']] = $question;
        $questionsByModule[$question['modul']][] = $question;
    }
    
    // Procesează fiecare test
    foreach ($tests as $test) {
        $raspunsuri = json_decode($test['raspunsuri_date'], true) ?: [];
        $raspunsuriCorecte = json_decode($test['raspunsuri_corecte'], true) ?: [];
        
        // Calculează scoruri pe module
        $moduleScores = [];
        for ($module = 1; $module <= 6; $module++) {
            $correctCount = 0;
            $totalCount = 0;
            
            if (isset($questionsByModule[$module])) {
                foreach ($questionsByModule[$module] as $question) {
                    $questionId = $question['id'];
                    $totalCount++;
                    
                    if (isset($raspunsuri[$questionId]) && 
                        isset($raspunsuriCorecte[$questionId]) &&
                        $raspunsuri[$questionId] === $raspunsuriCorecte[$questionId]) {
                        $correctCount++;
                    }
                }
            }
            
            $moduleScores[$module] = $totalCount > 0 ? "$correctCount/$totalCount" : "0/0";
        }
        
        // Creează string detaliat cu răspunsurile
        $detailedAnswers = [];
        foreach ($questions as $index => $question) {
            $questionId = $question['id'];
            $userAnswer = $raspunsuri[$questionId] ?? 'Nu a răspuns';
            $correctAnswer = $question['raspuns_corect'];
            $isCorrect = ($userAnswer === $correctAnswer) ? '✓' : '✗';
            
            $detailedAnswers[] = "Q" . ($index + 1) . ": " . $userAnswer . "(" . $correctAnswer . ")" . $isCorrect;
        }
        
        // Calculează procentul
        $percent = $test['scor'] ? round(($test['scor'] / 20) * 100, 1) : 0;
        
        // Creează rândul pentru CSV
        $row = [
            $test['id'],
            $test['nume'],
            $test['prenume'],
            $test['cnp'],
            $test['institutia'],
            $test['scor'] . '/20',
            $percent . '%',
            ucfirst($test['status']),
            $test['timp_start'] ? date('d.m.Y H:i:s', strtotime($test['timp_start'])) : '',
            $test['timp_finalizare'] ? date('d.m.Y H:i:s', strtotime($test['timp_finalizare'])) : '',
            $test['durata_minute'] ?: '',
            $test['ip_address'] ?: '',
            $moduleScores[1] ?? '0/0',
            $moduleScores[2] ?? '0/0', 
            $moduleScores[3] ?? '0/0',
            $moduleScores[4] ?? '0/0',
            $moduleScores[5] ?? '0/0',
            $moduleScores[6] ?? '0/0',
            implode(' | ', $detailedAnswers)
        ];
        
        fputcsv($output, $row);
    }
    
} catch (Exception $e) {
    // În caz de eroare, scrie o linie cu eroarea
    fputcsv($output, ['EROARE: Nu s-au putut exporta datele']);
}

fclose($output);
exit;
?>