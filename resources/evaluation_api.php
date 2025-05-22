<?php
// resources/evaluation_api.php
session_start();

// Includ sistemul de autentificare
require_once '../includes/auth.php';

// Verifică autentificarea folosind funcția din sistemul tău
if (!isSessionValid()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Neautentificat']);
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_questions':
        getQuestions();
        break;
    case 'start_test':
        startTest($input['student_data']);
        break;
    case 'save_progress':
        saveProgress($input['test_id'], $input['answers']);
        break;
    case 'finish_test':
        finishTest($input['test_id'], $input['answers']);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acțiune invalidă']);
}

// Restul funcțiilor rămân la fel...
function getQuestions() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM intrebari ORDER BY modul, id");
        $questions = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'questions' => $questions
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Eroare la încărcarea întrebărilor'
        ]);
    }
}

// ... restul funcțiilor rămân neschimbate

function startTest($studentData) {
    global $pdo;
    
    try {
        // Verifică dacă CNP-ul există deja cu test promovat
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM test_attempts WHERE cnp = ? AND status = 'promovat'");
        $stmt->execute([$studentData['cnp']]);
        
        if ($stmt->fetchColumn() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Există deja un test promovat pentru acest CNP'
            ]);
            return;
        }
        
        // Creează o nouă înregistrare pentru test
        $stmt = $pdo->prepare("
            INSERT INTO test_attempts (nume, prenume, cnp, institutia, ip_address, user_agent, session_id, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'in_progres')
        ");
        
        $stmt->execute([
            $studentData['nume'],
            $studentData['prenume'],
            $studentData['cnp'],
            $studentData['institutia'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            session_id()
        ]);
        
        $testId = $pdo->lastInsertId();
        
        // Selectează întrebările pentru test
        $stmt = $pdo->query("SELECT id FROM intrebari ORDER BY modul, id");
        $questionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Salvează ID-urile întrebărilor
        $stmt = $pdo->prepare("UPDATE test_attempts SET intrebari_ids = ? WHERE id = ?");
        $stmt->execute([json_encode($questionIds), $testId]);
        
        echo json_encode([
            'success' => true,
            'test_id' => $testId
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Eroare la începerea testului'
        ]);
    }
}

function saveProgress($testId, $answers) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE test_attempts SET raspunsuri_date = ? WHERE id = ?");
        $stmt->execute([json_encode($answers), $testId]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false]);
    }
}

function finishTest($testId, $answers) {
    global $pdo;
    
    try {
        // Obține datele testului
        $stmt = $pdo->prepare("SELECT * FROM test_attempts WHERE id = ?");
        $stmt->execute([$testId]);
        $test = $stmt->fetch();
        
        if (!$test) {
            echo json_encode(['success' => false, 'message' => 'Test negăsit']);
            return;
        }
        
        // Obține toate întrebările
        $stmt = $pdo->query("SELECT * FROM intrebari ORDER BY modul, id");
        $questions = $stmt->fetchAll();
        
        // Calculează scorul
        $score = 0;
        $correctAnswers = [];
        $wrongAnswers = [];
        
        foreach ($questions as $index => $question) {
            $questionId = $question['id'];
            $correctAnswer = $question['raspuns_corect'];
            $userAnswer = $answers[$questionId] ?? null;
            
            $correctAnswers[$questionId] = $correctAnswer;
            
            if ($userAnswer === $correctAnswer) {
                $score++;
            } else {
                $wrongAnswers[] = [
                    'question_number' => $index + 1,
                    'intrebarea' => $question['intrebarea'],
                    'your_answer' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                    'your_answer_text' => getAnswerText($question, $userAnswer),
                    'correct_answer_text' => getAnswerText($question, $correctAnswer),
                    'explicatie' => $question['explicatie']
                ];
            }
        }
        
        // Determină statusul
        $status = $score >= 16 ? 'promovat' : 'esuat';
        
        // Actualizează testul
        $stmt = $pdo->prepare("
            UPDATE test_attempts 
            SET scor = ?, raspunsuri_date = ?, raspunsuri_corecte = ?, 
                timp_finalizare = NOW(), status = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $score,
            json_encode($answers),
            json_encode($correctAnswers),
            $status,
            $testId
        ]);
        
        // Actualizează statisticile
        updateStatistics();
        
        echo json_encode([
            'success' => true,
            'results' => [
                'scor' => $score,
                'status' => $status,
                'wrong_answers' => $wrongAnswers
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Eroare la finalizarea testului'
        ]);
    }
}

function getAnswerText($question, $answer) {
    if (!$answer) return 'Nu ați răspuns';
    
    switch ($answer) {
        case 'a': return $question['varianta_a'];
        case 'b': return $question['varianta_b'];
        case 'c': return $question['varianta_c'];
        default: return 'Răspuns invalid';
    }
}

function updateStatistics() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_attempts,
                SUM(CASE WHEN status IN ('promovat', 'esuat') THEN 1 ELSE 0 END) as total_completed,
                SUM(CASE WHEN status = 'promovat' THEN 1 ELSE 0 END) as total_passed,
                AVG(CASE WHEN scor IS NOT NULL THEN scor ELSE 0 END) as average_score
            FROM test_attempts
        ");
        
        $stats = $stmt->fetch();
        
        $stmt = $pdo->prepare("
            UPDATE test_statistics 
            SET total_attempts = ?, total_completed = ?, total_passed = ?, average_score = ?
            WHERE id = 1
        ");
        
        $stmt->execute([
            $stats['total_attempts'],
            $stats['total_completed'],
            $stats['total_passed'],
            round($stats['average_score'], 2)
        ]);
    } catch (Exception $e) {
        // Log error but don't fail the main operation
        error_log("Error updating statistics: " . $e->getMessage());
    }
}
?>